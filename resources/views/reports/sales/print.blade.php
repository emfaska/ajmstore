<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan ({{ $start_date }} s.d {{ $end_date }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            background: #fff;
            padding: 20px;
            margin: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .title {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 10px;
            color: #555;
            margin: 0 0 20px 0;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
            padding: 10px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item:not(:last-child) {
            border-right: 1px solid #eee;
        }
        .summary-item span {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-item h4 {
            margin: 3px 0 0 0;
            font-size: 12px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #cbd5e1;
            padding: 6px;
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
        <h1 class="title">Laporan Penjualan</h1>
        <div class="subtitle">
            AJM STORE — Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} 
            @if($transaction_type)
                | Jenis: {{ $transaction_type === 'bengkel' ? 'Bengkel' : 'Penjualan Umum' }}
            @endif
        </div>
    </div>

    <!-- Summary KPI Block -->
    <div class="summary-box">
        <div class="summary-item" style="color: #1e3a8a;">
            <span>Total Omzet</span>
            <h4>Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h4>
        </div>
        <div class="summary-item" style="color: #b45309;">
            <span>Total Item Terjual</span>
            <h4>{{ number_format($totalItemTerjual, 0, ',', '.') }} pcs</h4>
        </div>
        <div class="summary-item" style="color: #065f46;">
            <span>Estimasi Laba Bersih</span>
            <h4>Rp {{ number_format($totalLaba, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Report Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="15%">No. Invoice</th>
                <th width="10%">Tanggal</th>
                <th width="10%">Jenis</th>
                <th width="15%">Pelanggan</th>
                <th width="10%" class="text-right">Subtotal</th>
                <th width="8%" class="text-right">Diskon</th>
                <th width="8%" class="text-right">Pajak</th>
                <th width="12%" class="text-right">Total Omzet</th>
                <th width="8%" class="text-center">Item</th>
                <th width="12%" class="text-right">Laba</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                @php
                    $qtySold = $sale->items->sum('quantity');
                    
                    // Hitung laba bersih level invoice
                    $saleLaba = 0;
                    foreach($sale->items as $item) {
                        $purchasePrice = (float) ($item->product->purchase_price ?? 0);
                        $saleLaba += ($item->selling_price - $purchasePrice) * $item->quantity;
                    }
                    $saleLaba -= $sale->discount;
                @endphp
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                    <td>{{ $sale->transaction_type === 'bengkel' ? 'Bengkel' : 'Umum' }}</td>
                    <td>{{ $sale->customer->name ?? 'Pelanggan Umum' }}</td>
                    <td class="text-right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #c53030;">
                        {{ $sale->discount > 0 ? '-' . number_format($sale->discount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $sale->tax > 0 ? number_format($sale->tax, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $qtySold }} pcs</td>
                    <td class="text-right font-bold" style="color: #15803d;">Rp {{ number_format($saleLaba, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="7">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totalItemTerjual, 0, ',', '.') }} pcs</td>
                <td class="text-right" style="color: #15803d; font-size: 11px;">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
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
