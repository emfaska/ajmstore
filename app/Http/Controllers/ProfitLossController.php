<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProfitLossController extends Controller
{
    protected SaleRepositoryInterface $saleRepository;
    protected IncomeRepositoryInterface $incomeRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;
    protected ExpenseRepositoryInterface $expenseRepository;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        IncomeRepositoryInterface $incomeRepository,
        PurchaseRepositoryInterface $purchaseRepository,
        ExpenseRepositoryInterface $expenseRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->incomeRepository = $incomeRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->expenseRepository = $expenseRepository;
    }

    /**
     * Display the Profit & Loss report.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $from = Carbon::parse("$year-$month-01")->startOfMonth()->toDateString();
        $to = Carbon::parse("$year-$month-01")->endOfMonth()->toDateString();

        // 1. Calculate Totals
        $totalSales = $this->saleRepository->sumTotal($from, $to);
        $totalIncome = $this->incomeRepository->sumTotal($from, $to);
        $totalPurchase = $this->purchaseRepository->sumTotal($from, $to);
        $totalExpense = $this->expenseRepository->sumTotal($from, $to);

        $pendapatan = $totalSales + $totalIncome;
        $pengeluaran = $totalPurchase + $totalExpense;
        $labaBersih = $pendapatan - $pengeluaran;

        // 2. Daily breakdowns for Chart.js
        $salesDaily = \App\Models\Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$from, $to])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $incomeDaily = \App\Models\Income::whereBetween('income_date', [$from, $to])
            ->selectRaw('DATE(income_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $purchasesDaily = \App\Models\Purchase::where('status', 'completed')
            ->whereBetween('purchase_date', [$from, $to])
            ->selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $expensesDaily = \App\Models\Expense::whereBetween('expense_date', [$from, $to])
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $daysInMonth = Carbon::parse("$year-$month-01")->daysInMonth;
        $chartLabels = [];
        $chartDataRevenue = [];
        $chartDataExpense = [];
        $chartDataProfit = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDateStr = Carbon::parse("$year-$month-$d")->toDateString();
            
            $dSales = (float) ($salesDaily[$currentDateStr] ?? 0);
            $dIncome = (float) ($incomeDaily[$currentDateStr] ?? 0);
            $dPurchases = (float) ($purchasesDaily[$currentDateStr] ?? 0);
            $dExpenses = (float) ($expensesDaily[$currentDateStr] ?? 0);

            $dRevenue = $dSales + $dIncome;
            $dExpense = $dPurchases + $dExpenses;
            $dProfit = $dRevenue - $dExpense;

            $chartLabels[] = $d;
            $chartDataRevenue[] = $dRevenue;
            $chartDataExpense[] = $dExpense;
            $chartDataProfit[] = $dProfit;
        }

        return view('reports.profit_loss.index', compact(
            'month',
            'year',
            'totalSales',
            'totalIncome',
            'totalPurchase',
            'totalExpense',
            'pendapatan',
            'pengeluaran',
            'labaBersih',
            'chartLabels',
            'chartDataRevenue',
            'chartDataExpense',
            'chartDataProfit'
        ));
    }

    /**
     * Print the Profit & Loss report (PDF).
     */
    public function exportPdf(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $from = Carbon::parse("$year-$month-01")->startOfMonth()->toDateString();
        $to = Carbon::parse("$year-$month-01")->endOfMonth()->toDateString();

        $totalSales = $this->saleRepository->sumTotal($from, $to);
        $totalIncome = $this->incomeRepository->sumTotal($from, $to);
        $totalPurchase = $this->purchaseRepository->sumTotal($from, $to);
        $totalExpense = $this->expenseRepository->sumTotal($from, $to);

        $pendapatan = $totalSales + $totalIncome;
        $pengeluaran = $totalPurchase + $totalExpense;
        $labaBersih = $pendapatan - $pengeluaran;

        return view('reports.profit_loss.print', compact(
            'month',
            'year',
            'totalSales',
            'totalIncome',
            'totalPurchase',
            'totalExpense',
            'pendapatan',
            'pengeluaran',
            'labaBersih'
        ));
    }
}
