<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return Customer::paginate(10);
    }

    public function show($id)
    {
        return Customer::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|in:A,A+,B,C',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'tax_id' => 'nullable|string',
            'payment_terms' => 'required|integer',
            'credit_limit' => 'required|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string',
        ]);

        return Customer::create($validated);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return $customer;
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer soft deleted']);
    }

  public function orderHistory(Request $request, $id)
{
    // ✅ Optional input validation (this is "the top")
    $request->validate([
        'status' => 'in:pending,confirmed,shipped,delivered,cancelled', // Adjust to your statuses
        'from_date' => 'date|nullable',
        'to_date' => 'date|nullable',
    ]);

    $query = Order::where('customer_id', $id)->with('items.product');

    // ✅ Apply status filter if present
    if ($request->has('status')) {
        $query->where('status', $request->input('status'));
    }

    // ✅ Apply date filters
    if ($request->has('from_date') && $request->has('to_date')) {
        $query->whereBetween('created_at', [
            $request->input('from_date'),
            $request->input('to_date'),
        ]);
    } elseif ($request->has('from_date')) {
        $query->whereDate('created_at', '>=', $request->input('from_date'));
    } elseif ($request->has('to_date')) {
        $query->whereDate('created_at', '<=', $request->input('to_date'));
    }

    return $query->get();
}


    public function creditStatus($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'credit_limit' => $customer->credit_limit,
            'current_balance' => $customer->current_balance,
        ]);
    }

    public function mapData()
    {
        return Customer::select('id', 'name', 'latitude', 'longitude')->get();
    }
}

