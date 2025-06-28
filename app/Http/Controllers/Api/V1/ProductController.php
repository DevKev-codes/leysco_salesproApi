<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'inventory.warehouse'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->warehouse, fn($q) => $q->whereHas('inventory', 
                fn($q) => $q->where('warehouse_id', $request->warehouse)))
            ->when($request->min_price, fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->stock_status === 'low', fn($q) => $q->lowStock())
            ->when($request->sort_by, function($q) use ($request) {
                foreach (explode(',', $request->sort_by) as $sort) {
                    $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
                    $column = ltrim($sort, '-');
                    $q->orderBy($column, $direction);
                }
            })
            ->paginate($request->per_page ?? 15);

        return new ProductCollection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load(['category', 'inventory.warehouse']));
    }

    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function() use ($request) {
            $product = Product::create($request->validated());
            
            // Initialize inventory for all warehouses
            foreach (Warehouse::all() as $warehouse) {
                $product->inventory()->create([
                    'warehouse_id' => $warehouse->id,
                    'quantity' => 0
                ]);
            }
            
            return $product;
        });

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function stock(Product $product)
    {
        return response()->json([
            'data' => $product->inventory()->with('warehouse')->get(),
            'total_quantity' => $product->inventory->sum('quantity'),
            'total_reserved' => $product->inventory->sum('reserved'),
            'total_available' => $product->inventory->sum('available'),
        ]);
    }

    public function reserveStock(Request $request, Product $product)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = $product->inventory()
            ->where('warehouse_id', $request->warehouse_id)
            ->firstOrFail();

        if ($inventory->available < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available',
                'available' => $inventory->available
            ], 422);
        }

        $inventory->increment('reserved', $request->quantity);

        return response()->json([
            'message' => 'Stock reserved successfully',
            'inventory' => $inventory->fresh()
        ]);
    }

    public function releaseStock(Request $request, Product $product)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = $product->inventory()
            ->where('warehouse_id', $request->warehouse_id)
            ->firstOrFail();

        if ($inventory->reserved < $request->quantity) {
            return response()->json([
                'message' => 'Not enough reserved stock to release',
                'reserved' => $inventory->reserved
            ], 422);
        }

        $inventory->decrement('reserved', $request->quantity);

        return response()->json([
            'message' => 'Stock released successfully',
            'inventory' => $inventory->fresh()
        ]);
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'inventory.warehouse'])
            ->lowStock()
            ->get();

        return new ProductCollection($products);
    }
}


