<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $range = $request->query('range', 'month'); // default: current month
        $cacheKey = "dashboard_summary_{$range}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($range) {
            [$start, $end] = $this->getDateRange($range);

            $orders = Order::whereBetween('created_at', [$start, $end])->get();

            $totalSales = $orders->sum('total_amount');
            $orderCount = $orders->count();
            $avgOrderValue = $orderCount ? $totalSales / $orderCount : 0;
            $inventoryTurnover = $this->calculateInventoryTurnover($start, $end);

            return response()->json([
                'total_sales' => round($totalSales, 2),
                'orders' => $orderCount,
                'average_order_value' => round($avgOrderValue, 2),
                'inventory_turnover_rate' => round($inventoryTurnover, 2),
            ], 200);
        });
    }

    public function salesPerformance(Request $request)
    {
        $range = $request->query('range', 'month');
        [$start, $end] = $this->getDateRange($range);

        $sales = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($sales, 200);
    }

    public function inventoryStatus()
    {
        return Cache::remember('inventory_status', now()->addMinutes(15), function () {
            return response()->json(
                Product::selectRaw('category, COUNT(*) as items, SUM(stock) as total_stock')
                    ->groupBy('category')
                    ->get(), 200
            );
        });
    }

    public function topProducts()
    {
        return Cache::remember('top_products', now()->addMinutes(10), function () {
            $top = OrderItem::with('product')
                ->selectRaw('product_id, SUM(quantity) as total_sold')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get();

            return response()->json($top, 200);
        });
    }

    private function getDateRange($range)
    {
        $today = now();

        return match ($range) {
            'today'   => [Carbon::today(), Carbon::today()->endOfDay()],
            'week'    => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'year'    => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function calculateInventoryTurnover($start, $end)
    {
        $costOfGoodsSold = OrderItem::whereBetween('created_at', [$start, $end])
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(\DB::raw('order_items.quantity * products.cost_price'));

        $avgInventory = Product::avg('stock');

        return $avgInventory > 0 ? $costOfGoodsSold / $avgInventory : 0;
    }
}
