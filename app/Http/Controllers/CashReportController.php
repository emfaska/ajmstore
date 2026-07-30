<?php

namespace App\Http\Controllers;

use App\Services\CashTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashReportController extends Controller
{
    protected CashTransactionService $cashTransactionService;

    public function __construct(CashTransactionService $cashTransactionService)
    {
        $this->cashTransactionService = $cashTransactionService;
    }

    /**
     * Display the cash report.
     */
    public function index(Request $request)
    {
        // 1. Resolve date range (defaults to first day of current month to today)
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Ensure proper chronological order
        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        // 2. Calculate Saldo Awal (Balance up to the day before start date)
        $startDateMinusOne = Carbon::parse($startDate)->subDay()->toDateString();
        // If start date is the very beginning, saldo awal is 0, but getRunningBalance handles it correctly
        $saldoAwal = $this->cashTransactionService->getRunningBalance($startDateMinusOne);

        // 3. Calculate Kas Masuk and Kas Keluar for the period
        $kasMasuk = $this->cashTransactionService->getTotalIncome($startDate, $endDate);
        $kasKeluar = $this->cashTransactionService->getTotalExpense($startDate, $endDate);

        // 4. Calculate Saldo Akhir (Balance up to end date)
        $saldoAkhir = $this->cashTransactionService->getRunningBalance($endDate);

        // 5. Fetch all cash transactions for the statement (Oldest to Newest)
        $transactions = \App\Models\CashTransaction::with('paymentMethod')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('reports.cash.index', compact(
            'startDate',
            'endDate',
            'saldoAwal',
            'kasMasuk',
            'kasKeluar',
            'saldoAkhir',
            'transactions'
        ));
    }

    /**
     * Export the cash report to PDF (via browser print).
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $startDateMinusOne = Carbon::parse($startDate)->subDay()->toDateString();
        $saldoAwal = $this->cashTransactionService->getRunningBalance($startDateMinusOne);
        $kasMasuk = $this->cashTransactionService->getTotalIncome($startDate, $endDate);
        $kasKeluar = $this->cashTransactionService->getTotalExpense($startDate, $endDate);
        $saldoAkhir = $this->cashTransactionService->getRunningBalance($endDate);

        $transactions = \App\Models\CashTransaction::with('paymentMethod')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('reports.cash.print', compact(
            'startDate',
            'endDate',
            'saldoAwal',
            'kasMasuk',
            'kasKeluar',
            'saldoAkhir',
            'transactions'
        ));
    }
}
