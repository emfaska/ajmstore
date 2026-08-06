<?php

namespace App\Http\Controllers;

use App\Services\CashTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashReportController extends Controller
{
    public function __construct(
        protected CashTransactionService $cashTransactionService
    ) {}

    /**
     * Resolve and validate a date range from request input.
     */
    private function resolveDateRange(Request $request): array
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        // Ensure chronological order
        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$startDate, $endDate];
    }

    /**
     * Collect all data needed by the cash report view.
     */
    private function buildReportData(string $startDate, string $endDate): array
    {
        // Saldo Awal = running balance up to the day before the period starts
        $beforeStart = Carbon::parse($startDate)->subDay()->toDateString();
        $saldoAwal   = $this->cashTransactionService->getRunningBalance($beforeStart);

        // Kas Masuk & Kas Keluar within the period
        $kasMasuk  = $this->cashTransactionService->getTotalIncome($startDate, $endDate);
        $kasKeluar = $this->cashTransactionService->getTotalExpense($startDate, $endDate);

        // Saldo Akhir = running balance up to the end of the period
        $saldoAkhir = $this->cashTransactionService->getRunningBalance($endDate);

        // All transactions in the period (oldest first for ledger view).
        // We request a large page size to effectively fetch all rows within the range.
        $paginator    = $this->cashTransactionService->filterByDate($startDate, $endDate, 9999);
        // Use the paginator's underlying Eloquent collection so we can call Eloquent methods like ->load()
        $transactions = $paginator->getCollection()
            ->load('paymentMethod')
            ->sortBy(fn($tx) => [$tx->transaction_date->toDateString(), $tx->id])
            ->values();

        // Monthly breakdown for chart (Kas Masuk vs Kas Keluar per day in range)
        $chartDays   = [];
        $chartMasuk  = [];
        $chartKeluar = [];

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);
        $diff  = $start->diffInDays($end);

        // Group by day (up to 30 visible labels) or by month if range > 60 days
        $groupByMonth = $diff > 60;

        // Build a lookup from transactions
        $masukByKey  = [];
        $keluarByKey = [];
        foreach ($transactions as $tx) {
            $key = $groupByMonth
                ? $tx->transaction_date->format('Y-m')
                : $tx->transaction_date->format('Y-m-d');

            if ($tx->type === 'debit') {
                $masukByKey[$key]  = ($masukByKey[$key]  ?? 0) + (float) $tx->amount;
            } else {
                $keluarByKey[$key] = ($keluarByKey[$key] ?? 0) + (float) $tx->amount;
            }
        }

        if ($groupByMonth) {
            $cursor = $start->copy()->startOfMonth();
            while ($cursor->lte($end)) {
                $key           = $cursor->format('Y-m');
                $chartDays[]   = $cursor->translatedFormat('M Y');
                $chartMasuk[]  = round($masukByKey[$key]  ?? 0);
                $chartKeluar[] = round($keluarByKey[$key] ?? 0);
                $cursor->addMonth();
            }
        } else {
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key           = $cursor->format('Y-m-d');
                $chartDays[]   = $cursor->format('d M');
                $chartMasuk[]  = round($masukByKey[$key]  ?? 0);
                $chartKeluar[] = round($keluarByKey[$key] ?? 0);
                $cursor->addDay();
            }
        }

        return compact(
            'startDate',
            'endDate',
            'saldoAwal',
            'kasMasuk',
            'kasKeluar',
            'saldoAkhir',
            'transactions',
            'chartDays',
            'chartMasuk',
            'chartKeluar',
        );
    }

    /**
     * Display the cash report page.
     */
    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $data = $this->buildReportData($startDate, $endDate);

        return view('reports.cash.index', $data);
    }

    /**
     * Render the printable PDF view of the cash report.
     */
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $data = $this->buildReportData($startDate, $endDate);

        return view('reports.cash.print', $data);
    }
}
