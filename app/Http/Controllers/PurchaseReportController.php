<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PurchaseReportController extends Controller
{
    protected PurchaseService $purchaseService;
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(
        PurchaseService $purchaseService,
        SupplierRepositoryInterface $supplierRepository
    ) {
        $this->purchaseService = $purchaseService;
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display the purchase report view.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $supplierId = $request->input('supplier_id', '');

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'supplier_id' => $supplierId,
        ];

        $reportData = $this->purchaseService->getPurchaseReport($filters);
        $suppliers = $this->supplierRepository->all()->sortBy('name');

        return view('reports.purchases.index', array_merge($filters, [
            'purchases' => $reportData['purchases'],
            'totalPembelian' => $reportData['total_pembelian'],
            'totalPengeluaran' => $reportData['total_pengeluaran'],
            'suppliers' => $suppliers,
        ]));
    }

    /**
     * Print the purchase report (PDF via browser print).
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $supplierId = $request->input('supplier_id', '');

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'supplier_id' => $supplierId,
        ];

        $reportData = $this->purchaseService->getPurchaseReport($filters);

        return view('reports.purchases.print', array_merge($filters, [
            'purchases' => $reportData['purchases'],
            'totalPembelian' => $reportData['total_pembelian'],
            'totalPengeluaran' => $reportData['total_pengeluaran'],
        ]));
    }

    /**
     * Export the purchase report to Excel-compatible CSV.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $supplierId = $request->input('supplier_id', '');

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'supplier_id' => $supplierId,
        ];

        $reportData = $this->purchaseService->getPurchaseReport($filters);
        
        $filename = 'Laporan_Pembelian_' . $startDate . '_to_' . $endDate . '.csv';

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
                'Tanggal Pembelian', 
                'Supplier', 
                'Status Transaksi',
                'Status Pembayaran',
                'Total Pembelian (Rp)', 
                'Total Pengeluaran / Terbayar (Rp)'
            ], ';');

            // Data Rows
            foreach ($reportData['purchases'] as $purchase) {
                $totalPaid = $purchase->cashTransactions->where('type', 'credit')->sum('amount');

                fputcsv($file, [
                    $purchase->invoice_number,
                    $purchase->purchase_date->format('d-m-Y'),
                    $purchase->supplier->name ?? 'N/A',
                    ucfirst($purchase->status),
                    ucfirst($purchase->payment_status),
                    number_format($purchase->total_amount, 0, '', ''),
                    number_format($totalPaid, 0, '', '')
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
                number_format($reportData['total_pembelian'], 0, '', ''),
                number_format($reportData['total_pengeluaran'], 0, '', '')
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
