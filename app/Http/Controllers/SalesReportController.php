<?php

namespace App\Http\Controllers;

use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display the sales report view.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $transactionType = $request->input('transaction_type', ''); // empty for all, or 'bengkel', 'penjualan_umum'

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transaction_type' => $transactionType,
        ];

        $reportData = $this->saleService->getSalesReport($filters);

        return view('reports.sales.index', array_merge($filters, [
            'sales' => $reportData['sales'],
            'totalOmzet' => $reportData['total_omzet'],
            'totalItemTerjual' => $reportData['total_item_terjual'],
            'totalLaba' => $reportData['total_laba'],
        ]));
    }

    /**
     * Print the sales report (PDF via browser print).
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $transactionType = $request->input('transaction_type', '');

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transaction_type' => $transactionType,
        ];

        $reportData = $this->saleService->getSalesReport($filters);

        return view('reports.sales.print', array_merge($filters, [
            'sales' => $reportData['sales'],
            'totalOmzet' => $reportData['total_omzet'],
            'totalItemTerjual' => $reportData['total_item_terjual'],
            'totalLaba' => $reportData['total_laba'],
        ]));
    }

    /**
     * Export the sales report to Excel-compatible CSV.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $transactionType = $request->input('transaction_type', '');

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transaction_type' => $transactionType,
        ];

        $reportData = $this->saleService->getSalesReport($filters);
        
        $filename = 'Laporan_Penjualan_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'No. Invoice', 
                'Tanggal', 
                'Jenis Transaksi', 
                'Pelanggan', 
                'Subtotal (Rp)', 
                'Diskon (Rp)', 
                'Pajak (Rp)', 
                'Total Omzet (Rp)', 
                'Item Terjual', 
                'Total Laba (Rp)'
            ], ';');

            // Data Rows
            foreach ($reportData['sales'] as $sale) {
                $itemQty = $sale->items->sum('quantity');
                
                // Calculate sale level profit
                $saleLaba = 0;
                foreach ($sale->items as $item) {
                    $purchasePrice = (float) ($item->product->purchase_price ?? 0);
                    $saleLaba += ($item->selling_price - $purchasePrice) * $item->quantity;
                }
                $saleLaba -= $sale->discount;

                fputcsv($file, [
                    $sale->invoice_number,
                    $sale->sale_date->format('d-m-Y'),
                    ($sale->transaction_type === 'bengkel') ? 'Bengkel' : 'Penjualan Umum',
                    $sale->customer->name ?? 'Pelanggan Umum',
                    number_format($sale->subtotal, 0, '', ''),
                    number_format($sale->discount, 0, '', ''),
                    number_format($sale->tax, 0, '', ''),
                    number_format($sale->total_amount, 0, '', ''),
                    $itemQty,
                    number_format($saleLaba, 0, '', '')
                ], ';');
            }

            // Summary Row
            fputcsv($file, [], ';');
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($reportData['total_omzet'], 0, '', ''),
                $reportData['total_item_terjual'],
                number_format($reportData['total_laba'], 0, '', '')
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
