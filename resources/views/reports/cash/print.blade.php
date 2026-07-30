<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas ({{ $startDate }} s.d {{ $endDate }})</title>
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
            color: #666;
            margin: 0 0 20px 0;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fcfcfc;
            padding: 12px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item:not(:last-child) {
            border-right: 1px solid #eee;
        }
        .summary-item span {
            font-size: 10px;
            color: #777;
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
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }
        .table th {
            background-color: #f7fafc;
            font-weight: bold;
        }
        .table tr.total-row {
            background-color: #edf2f7;
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

    <!-- Header -->
    <div class="text-center">
        <h1 class="title">Laporan Arus Kas</h1>
        <div class="subtitle">AJM STORE — Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
    </div>

    <!-- Summary Widgets -->
    <div class="summary-box">
        <div class="summary-item">
            <span>Saldo Awal</span>
            <h4>Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h4>
        </div>
        <div class="summary-item" style="color: #2f855a;">
            <span>Total Kas Masuk</span>
            <h4>+ Rp {{ number_format($kasMasuk, 0, ',', '.') }}</h4>
        </div>
        <div class="summary-item" style="color: #c53030;">
            <span>Total Kas Keluar</span>
            <h4>- Rp {{ number_format($kasKeluar, 0, ',', '.') }}</h4>
        </div>
        <div class="summary-item" style="color: #2b6cb0;">
            <span>Saldo Akhir</span>
            <h4>Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Statement Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="15%">Referensi</th>
                <th width="12%">Cara Bayar</th>
                <th width="13%" class="text-right">Debit (+)</th>
                <th width="13%" class="text-right">Credit (-)</th>
                <th width="15%" class="text-right">Saldo Kas</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal Row -->
            <tr style="background: #f7fafc; font-style: italic;">
                <td colspan="4" class="font-bold">SALDO SEBELUM PERIODE {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @php
                $runningBalance = $saldoAwal;
            @endphp

            @foreach($transactions as $tx)
                @php
                    if ($tx->type === 'debit') {
                        $runningBalance += $tx->amount;
                    } else {
                        $runningBalance -= $tx->amount;
                    }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $tx->description }}</td>
                    <td>
                        @if($tx->referenceable_type)
                            @php
                                $typeParts = explode('\\', $tx->referenceable_type);
                                $typeName = end($typeParts);
                            @endphp
                            {{ $typeName }} #{{ $tx->referenceable_id }}
                        @else
                            Manual
                        @endif
                    </td>
                    <td>{{ $tx->paymentMethod->name ?? '-' }}</td>
                    <td class="text-right" style="color: #2f855a;">
                        {{ ($tx->type === 'debit') ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right" style="color: #c53030;">
                        {{ ($tx->type === 'credit') ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Saldo Akhir Row -->
            <tr class="total-row">
                <td colspan="4">SALDO HINGGA PERIODE {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
                <td class="text-right" style="color: #2f855a;">+ Rp {{ number_format($kasMasuk, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #c53030;">- Rp {{ number_format($kasKeluar, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #2b6cb0; font-size: 12px;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Printing Action Button (no-print) -->
    <div class="no-print text-center" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; background: #3182ce; color: #fff; border: 0; border-radius: 4px; cursor: pointer;">Cetak</button>
        <button onclick="window.close()" style="padding: 6px 16px; font-weight: bold; background: #e53e3e; color: #fff; border: 0; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

</body>
</html>
