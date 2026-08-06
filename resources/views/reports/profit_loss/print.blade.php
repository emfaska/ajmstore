<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan Laba Rugi - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            padding: 20px
        }

        .text-right {
            text-align: right
        }

        .text-center {
            text-align: center
        }

        .summary {
            display: flex;
            gap: 12px;
            margin-bottom: 12px
        }

        .card {
            flex: 1;
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 6px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px
        }
    </style>
</head>

<body onload="window.print()">
    <div class="text-center">
        <h2>Laporan Laba Rugi</h2>
        <div>Periode: {{ \Carbon\Carbon::parse("{$year}-{$month}-01")->translatedFormat('F Y') }}</div>
    </div>

    <div class="summary" style="margin-top:14px">
        <div class="card">
            <div class="text-xs">Total Penjualan</div>
            <div class="text-lg font-bold">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="text-xs">Pemasukan Lain</div>
            <div class="text-lg font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="text-xs">Pendapatan</div>
            <div class="text-lg font-bold">Rp {{ number_format($pendapatan, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="summary">
        <div class="card">
            <div class="text-xs">Total Pembelian</div>
            <div class="text-lg font-bold">Rp {{ number_format($totalPurchase, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="text-xs">Pengeluaran Operasional</div>
            <div class="text-lg font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="text-xs">Pengeluaran</div>
            <div class="text-lg font-bold">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-top:12px;padding:10px;border:1px solid #e5e7eb;border-radius:6px">
        <h3>Laba Bersih: Rp {{ number_format($labaBersih, 0, ',', '.') }}</h3>
    </div>

    <div style="margin-top:18px">
        <h4>Rincian Bulanan (grafik ada di versi web)</h4>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Laba</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $days = count($chartLabels ?? []);
                @endphp
                @for ($i = 0; $i < $days; $i++)
                    <tr>
                        <td class="text-center">{{ $chartLabels[$i] }}</td>
                        <td class="text-right">Rp {{ number_format($chartDataRevenue[$i] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($chartDataExpense[$i] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($chartDataProfit[$i] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

</body>

</html>
