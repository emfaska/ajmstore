<?php

namespace App\Services;

use App\Models\Sale;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        protected SaleRepositoryInterface            $saleRepository,
        protected PurchaseRepositoryInterface        $purchaseRepository,
        protected ExpenseRepositoryInterface         $expenseRepository,
        protected CashTransactionRepositoryInterface $cashTransactionRepository,
        protected ProductRepositoryInterface         $productRepository,
    ) {}

    /**
     * Collect all data needed by the analytics dashboard.
     */
    public function getDashboardData(): array
    {
        // ── KPI Cards ────────────────────────────────────────────────────────
        $totalSales     = $this->saleRepository->sumTotal('1970-01-01', '2099-12-31');
        $totalPurchases = $this->purchaseRepository->sumTotal('1970-01-01', '2099-12-31');
        $totalExpenses  = $this->expenseRepository->sumTotal('1970-01-01', '2099-12-31');

        $debit     = $this->cashTransactionRepository->sumDebit('1970-01-01', '2099-12-31');
        $credit    = $this->cashTransactionRepository->sumCredit('1970-01-01', '2099-12-31');
        $saldoKas  = $debit - $credit;

        // ── Product Stats ────────────────────────────────────────────────────
        $totalProducts = $this->productRepository->count();
        $lowStockCount = $this->productRepository->getLowStock()->count();

        // ── Latest 10 Transactions ───────────────────────────────────────────
        // paginate() returns LengthAwarePaginator; take first page (10 rows)
        $latestTransactions = $this->saleRepository
            ->paginate(10, ['customer', 'paymentMethod']);

        // ── Sales Chart – last 30 days ───────────────────────────────────────
        $startDate = now()->subDays(29)->startOfDay();
        $endDate   = now()->endOfDay();

        $salesTrend = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartData   = [];

        for ($i = 29; $i >= 0; $i--) {
            $dateObj       = now()->subDays($i);
            $formattedDate = $dateObj->format('Y-m-d');
            $chartLabels[] = $dateObj->format('d M');
            $chartData[]   = (float) ($salesTrend[$formattedDate] ?? 0);
        }

        // ── Today's stats (sparkline context) ───────────────────────────────
        $today        = Carbon::today()->toDateString();
        $salesToday   = $this->saleRepository->sumTotal($today, $today);

        return compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'saldoKas',
            'totalProducts',
            'lowStockCount',
            'latestTransactions',
            'chartLabels',
            'chartData',
            'salesToday',
        );
    }
}
