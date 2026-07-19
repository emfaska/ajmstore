<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentAnalysisRepository implements AnalysisRepositoryInterface
{
    /**
     * Get revenue summary (total sales, completed orders count, AOV, total items sold) in a date range.
     */
    public function getRevenueSummary(Carbon $startDate, Carbon $endDate): array
    {
        $summary = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue, COUNT(*) as total_orders')
            ->first();

        $totalRevenue = (float) $summary->total_revenue;
        $totalOrders = (int) $summary->total_orders;
        $aov = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $totalItemsSold = (int) OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        })->sum('quantity');

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $aov,
            'total_items_sold' => $totalItemsSold,
        ];
    }

    /**
     * Get daily sales trends (date, total transactions, total amount) in a date range.
     */
    public function getDailySalesTrend(Carbon $startDate, Carbon $endDate): array
    {
        $trends = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // Fill in missing dates to prevent gaps in charts
        $result = [];
        $currentDate = clone $startDate;
        while ($currentDate->lte($endDate)) {
            $formattedDate = $currentDate->format('Y-m-d');
            $trend = $trends->get($formattedDate);

            $result[] = [
                'date' => $formattedDate,
                'revenue' => $trend ? (float) $trend->revenue : 0.0,
                'orders_count' => $trend ? (int) $trend->orders_count : 0,
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    /**
     * Get top products by quantity sold in a date range.
     */
    public function getTopProducts(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        return OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        })
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->selectRaw('products.id, products.name, products.sku, SUM(order_items.quantity) as total_quantity, SUM(order_items.quantity * order_items.price) as total_revenue')
        ->groupBy('products.id', 'products.name', 'products.sku')
        ->orderBy('total_quantity', 'desc')
        ->limit($limit)
        ->get()
        ->toArray();
    }

    /**
     * Get transaction distribution by order status in a date range.
     */
    public function getOrderStatusDistribution(Carbon $startDate, Carbon $endDate): array
    {
        $distribution = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'completed' => (int) ($distribution['completed'] ?? 0),
            'pending' => (int) ($distribution['pending'] ?? 0),
            'cancelled' => (int) ($distribution['cancelled'] ?? 0),
        ];
    }
}
