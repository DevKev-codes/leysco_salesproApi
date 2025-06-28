<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;

class ProductStockController extends Controller
{
    /**
     * Reserve stock for a product.
     */
    public function reserve(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);
        $inventory = Inventory::where('product_id', $product->id)
                              ->where('warehouse_id', $request->warehouse_id)
                              ->firstOrFail();

        $available = $inventory->stock_quantity - $inventory->reserved_quantity;

        if ($request->quantity > $available) {
            return response()->json([
                'error' => 'Insufficient available stock in this warehouse.'
            ], 422);
        }

        $inventory->reserved_quantity += $request->quantity;
        $inventory->save();

        return response()->json([
            'message' => 'Stock reserved successfully.',
            'reserved_quantity' => $inventory->reserved_quantity
        ], 200);
    }

    /**
     * Release reserved stock.
     */
    public function release(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);
        $inventory = Inventory::where('product_id', $product->id)
                              ->where('warehouse_id', $request->warehouse_id)
                              ->firstOrFail();

        $releaseQty = min($request->quantity, $inventory->reserved_quantity);
        $inventory->reserved_quantity -= $releaseQty;
        $inventory->save();

        return response()->json([
            'message' => 'Reserved stock released successfully.',
            'remaining_reserved_quantity' => $inventory->reserved_quantity
        ], 200);
    }
}

