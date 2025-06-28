<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\OrderConfirmation;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('customer', 'items.product')->filter($request)->paginate(15);
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::with('customer', 'items.product')->find($id);
        return $order ? response()->json($order) : response()->json(['error' => 'Not Found'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
        ]);

        return DB::transaction(function () use ($validated) {
            $customer = Customer::findOrFail($validated['customer_id']);
            $orderTotal = 0;
            $stockErrors = [];

            // Stock + Pricing logic
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $availableStock = $product->available_stock;
                if ($item['quantity'] > $availableStock) {
                    $stockErrors[] = "{$product->name} has insufficient stock.";
                }
                $subtotal = $product->price * $item['quantity'];
                $orderTotal += $subtotal;
            }

            if ($stockErrors) {
                return response()->json(['errors' => $stockErrors], 422);
            }

            // Apply discount
            $discountAmount = 0;
            if (isset($validated['discount'])) {
                if ($validated['discount_type'] === 'percent') {
                    $discountAmount = $orderTotal * ($validated['discount'] / 100);
                } else {
                    $discountAmount = $validated['discount'];
                }
                $orderTotal -= $discountAmount;
            }

            // Tax
            $taxRate = config('sales.tax_rate', 16); // default 16%
            $taxAmount = $orderTotal * ($taxRate / 100);
            $grandTotal = $orderTotal + $taxAmount;

            // Credit validation
            if ($customer->available_credit < $grandTotal) {
                return response()->json(['error' => 'Customer credit limit exceeded'], 403);
            }

            // Create Order
            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'order_number' => 'ORD-' . now()->timestamp,
                'subtotal' => $orderTotal,
                'discount' => $discountAmount,
                'tax' => $taxAmount,
                'total' => $grandTotal,
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => Product::find($item['product_id'])->price,
                ]);

                // Reserve Stock
                Product::find($item['product_id'])->decrement('available_stock', $item['quantity']);
            }

            // Send confirmation email (queue)
            Mail::to($customer->email)->queue(new OrderConfirmation($order));


            return response()->json(['message' => 'Order created', 'order' => $order], 201);
        });
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        // Disallow if already shipped
        if ($order->status === 'shipped' && $request->status !== 'delivered') {
            return response()->json(['error' => 'Order already shipped and cannot be changed'], 400);
        }

        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }

    public function invoice($id)
    {
        $order = Order::with('items.product', 'customer')->findOrFail($id);
        return response()->json([
            'order' => $order,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
        ]);
    }

    public function calculateTotal(Request $request)
    {
        // Same logic as store() but without saving order
        // Return subtotal, tax, discount breakdown
    }
}

