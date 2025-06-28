<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Common date range processor
    protected function getDateRange(Request $request)
    {
        $range = $request->input('range', 'today');
        
        return match ($range) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [
                Carbon::parse($request->input('from', Carbon::today())),
                Carbon::parse($request->input('to', Carbon::now()))
            ],
        };
    }

    // Overall sales metrics
    public function summary(Request $request)
    {
        $cacheKey = 'dashboard_summary_' . $request->input('range', 'today');
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
            [$startDate, $endDate] = $this->getDateRange($request);
            
            $result = DB::table('orders')
                ->selectRaw('SUM(total_amount) as total_sales')
                ->selectRaw('COUNT(*) as total_orders')
                ->selectRaw('SUM(total_amount)/COUNT(*) as average_order_value')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->first();
            
            return response()->json([
                'total_sales' => $result->total_sales ?? 0,
                'total_orders' => $result->total_orders ?? 0,
                'average_order_value' => $result->average_order_value ?? 0,
                'timeframe' => [
                    'start' => $startDate->toDateTimeString(),
                    'end' => $endDate->toDateTimeString()
                ]
            ]);
        });
    }

    // Sales performance data
    public function salesPerformance(Request $request)
    {
        $cacheKey = 'sales_performance_' . $request->input('range', 'month');
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($request) {
            [$startDate, $endDate] = $this->getDateRange($request);
            
            $salesData = DB::table('orders')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total_sales'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            return response()->json([
                'data' => $salesData,
                'timeframe' => [
                    'start' => $startDate->toDateTimeString(),
                    'end' => $endDate->toDateTimeString()
                ]
            ]);
        });
    }

    // Inventory status
    public function inventoryStatus()
    {
        return Cache::remember('inventory_status', now()->addHour(), function () {
            $inventory = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'categories.name as category',
                    DB::raw('SUM(products.quantity) as total_quantity'),
                    DB::raw('COUNT(products.id) as product_count'),
                    DB::raw('SUM(products.quantity * products.price) as inventory_value')
                )
                ->groupBy('categories.name')
                ->get();
            
            return response()->json($inventory);
        });
    }

    // Top products
    public function topProducts(Request $request)
    {
        $cacheKey = 'top_products_' . $request->input('range', 'month');
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
            [$startDate, $endDate] = $this->getDateRange($request);
            
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.name',
                    'products.sku',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->whereBetween('order_items.created_at', [$startDate, $endDate])
                ->groupBy('products.id', 'products.name', 'products.sku')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
            
            return response()->json($topProducts);
        });
    }
}
