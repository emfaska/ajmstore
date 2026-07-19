<?php

namespace App\Repositories;

use Carbon\Carbon;

interface AnalysisRepositoryInterface
{
    /**
     * Get revenue summary (total sales, completed orders count, AOV, total items sold) in a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getRevenueSummary(Carbon $startDate, Carbon $endDate): array;

    /**
     * Get daily sales trends (date, total transactions, total amount) in a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getDailySalesTrend(Carbon $startDate, Carbon $endDate): array;

    /**
     * Get top products by quantity sold in a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return array
     */
    public function getTopProducts(Carbon $startDate, Carbon $endDate, int $limit = 5): array;

    /**
     * Get transaction distribution by order status in a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getOrderStatusDistribution(Carbon $startDate, Carbon $endDate): array;
}
