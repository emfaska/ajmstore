<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected SaleRepositoryInterface $saleRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;
    protected ExpenseRepositoryInterface $expenseRepository;
    protected CashTransactionRepositoryInterface $cashTransactionRepository;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        PurchaseRepositoryInterface $purchaseRepository,
        ExpenseRepositoryInterface $expenseRepository,
        CashTransactionRepositoryInterface $cashTransactionRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->expenseRepository = $expenseRepository;
        $this->cashTransactionRepository = $cashTransactionRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Display the analytical dashboard.
     */
    public function index()
    {
        // 1. KPI Calculations using Repositories
        $totalSales = $this->saleRepository->sumTotal('1970-01-01', '2099-12-31');
        $totalPurchases = $this->purchaseRepository->sumTotal('1970-01-01', '2099-12-31');
        $totalExpenses = $this->expenseRepository->sumTotal('1970-01-01', '2099-12-31');
        
        $saldoKas = $this->cashTransactionRepository->sumDebit('1970-01-01', '2099-12-31') 
            - $this->cashTransactionRepository->sumCredit('1970-01-01', '2099-12-31');

        $totalProducts = $this->productRepository->count();
        $lowStockCount = $this->productRepository->getLowStock()->count();

        // 2. Load 10 Latest Transactions (Sales)
        $latestTransactions = $this->saleRepository->paginate(10, ['customer', 'paymentMethod']);

        // 3. Daily Sales Trend for the last 30 days
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $salesTrend = \App\Models\Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $formattedDate = $dateObj->format('Y-m-d');
            $chartLabels[] = $dateObj->format('d M');
            $chartData[] = (float) ($salesTrend[$formattedDate] ?? 0);
        }

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'saldoKas',
            'totalProducts',
            'lowStockCount',
            'latestTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}
