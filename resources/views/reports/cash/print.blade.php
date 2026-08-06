<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas — AJM Store ({{ $startDate }} s.d {{ $endDate }})</title>
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1a202c;
            background: #fff;
            padding: 28px 32px;
        }

        /* ── Header ── */
        .report-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2b6cb0; padding-bottom: 14px; }
        .store-name   { font-size: 14px; font-weight: bold; letter-spacing: .06em; text-transform: uppercase; color: #1a202c; }
        .report-title { font-size: 16px; font-weight: bold; text-transform: uppercase; color: #2b6cb0; margin: 4px 0 2px; }
        .report-period{ font-size: 10px; color: #4a5568; }
        .print-time   { font-size: 9px; color: #a0aec0; margin-top: 3px; }

        /* ── KPI Summary Box ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .summary-item {
            padding: 10px 12px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }
        .summary-item:last-child { border-right: none; }
        .summary-label { font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #718096; font-weight: bold; }
        .summary-value { font-size: 13px; font-weight: bold; margin-top: 4px; }
        .summary-value.opening  { color: #2d3748; }
        .summary-value.income   { color: #2f855a; }
        .summary-value.expense  { color: #c53030; }
        .summary-value.closing  { color: #2b6cb0; }

        /* ── Ledger Table ── */
        .table { width: 100%; border-collapse: collapse; margin-top: 0; }
        .table th {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            padding: 6px 8px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #2b6cb0;
        }
        .table td {
            border: 1px solid #e2e8f0;
            padding: 5.5px 8px;
            font-size: 10px;
            vertical-align: middle;
        }
        .table tr:nth-child(even) td { background: #f7fafc; }

        /* Special rows */
        .row-opening td   { background: #f7fafc !important; font-style: italic; color: #4a5568; font-weight: bold; }
        .row-closing td   { background: #ebf8ff !important; font-weight: bold; color: #2b6cb0; }
        .row-closing-label{ font-size: 9.5px; text-transform: uppercase; letter-spacing: .05em; }

        /* Alignment helpers */
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .font-bold   { font-weight: bold; }

        /* Colour helpers */
        .clr-income  { color: #2f855a; }
        .clr-expense { color: #c53030; }
        .clr-saldo   { color: #2b6cb0; }
        .clr-neg     { color: #c53030; }
        .clr-muted   { color: #a0aec0; }

        /* ── Type badge ── */
        .badge {
            display: inline-block;
            font-size: 8.5px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .badge-debit  { background: #c6f6d5; color: #276749; }
        .badge-credit { background: #fed7d7; color: #9b2c2c; }

        /* ── Footer ── */
        .report-footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-note { font-size: 9px; color: #a0aec0; }
        .signature-block { text-align: center; font-size: 10px; }
        .signature-block .sig-line {
            width: 140px;
            border-top: 1px solid #2d3748;
            margin-top: 40px;
            padding-top: 4px;
        }

        /* ── No-print buttons ── */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 999;
        }
        .btn {
            padding: 8px 18px;
            font-size: 12px;
            font-weight: bold;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            letter-spacing: .03em;
        }
        .btn-print { background: #2b6cb0; color: #fff; }
        .btn-close { background: #e53e3e; color: #fff; }

        @media print {
            body { padding: 12px 16px; }
            .no-print { display: none !important; }
            .table tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    {{-- ── Report Header ── --}}
    <div class="report-header">
        <div class="store-name">AJM Store</div>
        <div class="report-title">Laporan Arus Kas</div>
        <div class="report-period">
            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
            s.d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        </div>
        <div class="print-time">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    {{-- ── KPI Summary ── --}}
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-label">Saldo Awal</div>
            <div class="summary-value opening">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Kas Masuk</div>
            <div class="summary-value income">+ Rp {{ number_format($kasMasuk, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Kas Keluar</div>
            <div class="summary-value expense">- Rp {{ number_format($kasKeluar, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Saldo Akhir</div>
            <div class="summary-value closing">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ── Ledger Table ── --}}
    <table class="table">
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="32%">Keterangan</th>
                <th width="13%">Referensi</th>
                <th width="10%">Metode</th>
                <th width="7%"  class="text-center">Tipe</th>
                <th width="12%" class="text-right">Debit (+)</th>
                <th width="12%" class="text-right">Kredit (-)</th>
                <th width="14%" class="text-right">Saldo Kas</th>
            </tr>
        </thead>
        <tbody>

            {{-- Saldo Awal --}}
            <tr class="row-opening">
                <td colspan="5">
                    Saldo sebelum periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                </td>
                <td class="text-right clr-muted">—</td>
                <td class="text-right clr-muted">—</td>
                <td class="text-right clr-saldo">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @php $runningBalance = $saldoAwal; @endphp

            @forelse($transactions as $index => $tx)
                @php
                    $runningBalance += ($tx->type === 'debit') ? $tx->amount : -$tx->amount;
                    $typeParts = $tx->referenceable_type ? explode('\\', $tx->referenceable_type) : [];
                    $refLabel  = $tx->referenceable_type
                        ? (end($typeParts) . ' #' . $tx->referenceable_id)
                        : 'Manual';
                @endphp
                <tr>
                    <td>{{ $tx->transaction_date->translatedFormat('d M Y') }}</td>
                    <td>{{ $tx->description ?? '—' }}</td>
                    <td>{{ $refLabel }}</td>
                    <td>{{ $tx->paymentMethod->name ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $tx->type === 'debit' ? 'badge-debit' : 'badge-credit' }}">
                            {{ $tx->type === 'debit' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right clr-income font-bold">
                        {{ $tx->type === 'debit' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '—' }}
                    </td>
                    <td class="text-right clr-expense font-bold">
                        {{ $tx->type === 'credit' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '—' }}
                    </td>
                    <td class="text-right font-bold {{ $runningBalance < 0 ? 'clr-neg' : 'clr-saldo' }}">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 16px; color: #a0aec0; font-style: italic;">
                        Tidak ada transaksi kas pada periode ini.
                    </td>
                </tr>
            @endforelse

            {{-- Saldo Akhir --}}
            <tr class="row-closing">
                <td colspan="5" class="row-closing-label">
                    Saldo Akhir Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                </td>
                <td class="text-right clr-income font-bold">
                    Rp {{ number_format($kasMasuk, 0, ',', '.') }}
                </td>
                <td class="text-right clr-expense font-bold">
                    Rp {{ number_format($kasKeluar, 0, ',', '.') }}
                </td>
                <td class="text-right clr-saldo font-bold" style="font-size: 12px;">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── Footer / Signature ── --}}
    <div class="report-footer">
        <div class="footer-note">
            * Laporan ini digenerate secara otomatis oleh sistem AJM Store.<br>
            * Saldo berjalan dihitung dari total debit dikurangi total kredit kumulatif.
        </div>
        <div class="signature-block">
            <div>Mengetahui,</div>
            <div class="sig-line">Pemilik / Manager</div>
        </div>
    </div>

    {{-- ── Print Action Buttons (hidden on print) ── --}}
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">
            🖨 Cetak PDF
        </button>
        <button class="btn btn-close" onclick="window.close()">
            ✕ Tutup
        </button>
    </div>

</body>
</html>
