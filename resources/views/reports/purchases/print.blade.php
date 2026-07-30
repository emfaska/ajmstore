<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian Barang ({{ $start_date }} s.d {{ $end_date }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            background: #fff;
            padding: 20px;
            margin: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11px;
            color: #555;
            margin: 0 0 20px 0;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            padding: 12px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item:not(:last-child) {
            border-right: 1px solid #e2e8f0;
        }
        .summary-item span {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
        }
        .summary-item h4 {
            margin: 4px 0 0 0;
            font-size: 13px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .table tr.total-row {
            background-color: #e2e8f0;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Kop Header -->
    <div class="text-center">
        <h1 class="title">Laporan Pembelian Barang</h1>
        <div class="subtitle">
            AJM STORE — Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        </div>
    </div>

    <!-- Summary Widgets -->
    <div class="summary-box">
        <div class="summary-item" style="color: #312e81;">
            <span>Total Pembelian (Invoice)</span>
            <h4>Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h4>
        </div>
        <div class="summary-item" style="color: #991b1b;">
            <span>Total Pengeluaran Kas (Terbayar)</span>
            <h4>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Report Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="15%">No. Invoice</th>
                <th width="15%">Tanggal Pembelian</th>
                <th width="25%">Supplier</th>
                <th width="15%" class="text-center">Status Transaksi</th>
                <th width="15%" class="text-center">Status Pembayaran</th>
                <th width="15%" class="text-right">Total Pembelian</th>
                <th width="15%" class="text-right">Total Terbayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                @php
                    $totalPaid = $purchase->cashTransactions->where('type', 'credit')->sum('amount');
                @endphp
                <tr>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                    <td class="font-bold">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ ucfirst($purchase->status) }}</td>
                    <td class="text-center">{{ ucfirst($purchase->payment_status) }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #b91c1c;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="5">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b91c1c;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Print Action Buttons -->
    <div class="no-print text-center" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; background: #2563eb; color: #fff; border: 0; border-radius: 4px; cursor: pointer;">Cetak</button>
        <button onclick="window.close()" style="padding: 6px 16px; font-weight: bold; background: #dc2626; color: #fff; border: 0; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

</body>
</html>
