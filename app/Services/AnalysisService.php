<?php

namespace App\Services;

use App\Repositories\AnalysisRepositoryInterface;
use Carbon\Carbon;

class AnalysisService
{
    protected $analysisRepository;

    /**
     * Create a new AnalysisService instance.
     *
     * @param AnalysisRepositoryInterface $analysisRepository
     */
    public function __construct(AnalysisRepositoryInterface $analysisRepository)
    {
        $this->analysisRepository = $analysisRepository;
    }

    /**
     * Get all dashboard analysis data.
     *
     * @param array $filters
     * @return array
     */
    public function getDashboardData(array $filters): array
    {
        // 1. Parse and default dates
        $startDateStr = $filters['start_date'] ?? null;
        $endDateStr = $filters['end_date'] ?? null;

        if ($startDateStr) {
            $startDate = Carbon::parse($startDateStr)->startOfDay();
        } else {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
        }

        if ($endDateStr) {
            $endDate = Carbon::parse($endDateStr)->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
        }

        // Ensure date logical ordering if wrong input somehow bypassed validation
        if ($startDate->gt($endDate)) {
            $temp = $startDate;
            $startDate = $endDate->copy()->startOfDay();
            $endDate = $temp->copy()->endOfDay();
        }

        // 2. Fetch data via Repository
        $summary = $this->analysisRepository->getRevenueSummary($startDate, $endDate);
        $trends = $this->analysisRepository->getDailySalesTrend($startDate, $endDate);
        $topProducts = $this->analysisRepository->getTopProducts($startDate, $endDate);
        $statusDistribution = $this->analysisRepository->getOrderStatusDistribution($startDate, $endDate);

        // 3. Formulate analytics metrics package
        return [
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => $summary,
            'trends' => $trends,
            'top_products' => $topProducts,
            'status_distribution' => $statusDistribution,
        ];
    }
}
