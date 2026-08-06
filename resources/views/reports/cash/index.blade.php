@extends('layouts.app')

@section('title', 'Laporan Arus Kas — AJM Store')

@push('styles')
<style>
    /* ── KPI gradient cards ── */
    .kpi-slate   { background: linear-gradient(135deg, #334155 0%, #475569 100%); }
    .kpi-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .kpi-rose    { background: linear-gradient(135deg, #be123c 0%, #f43f5e 100%); }
    .kpi-blue    { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); }

    @keyframes fade-up {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0);    }
    }
    .kpi-card { animation: fade-up .4s ease both; }
    .kpi-card:nth-child(1) { animation-delay: .05s; }
    .kpi-card:nth-child(2) { animation-delay: .10s; }
    .kpi-card:nth-child(3) { animation-delay: .15s; }
    .kpi-card:nth-child(4) { animation-delay: .20s; }
    .kpi-card:hover { transform: translateY(-3px); transition: transform .2s; }

    /* ── Table row hover ── */
    .tx-row { transition: background .12s ease; }

    /* ── Debit / Credit badges ── */
    .badge-debit  { background: #d1fae5; color: #065f46; }
    .badge-credit { background: #fee2e2; color: #991b1b; }

    /* ── Running saldo colour helper ── */
    .saldo-positive { color: #1d4ed8; }
    .saldo-negative { color: #be123c; }

    /* ── Filter card ── */
    .filter-card { background: white; border-radius: 1rem; border: 1px solid #f0f0f0;
                   box-shadow: 0 1px 4px rgba(0,0,0,.06); padding: 1.25rem; }

    /* ── Period badge ── */
    .period-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .72rem; font-weight: 600; letter-spacing: .04em;
        background: #eff6ff; color: #1d4ed8;
        padding: .3rem .8rem; border-radius: 9999px;
    }
</style>
@endpush

@section('content')
<div class="space-y-7">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Laporan Arus Kas</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor kas masuk, kas keluar, dan saldo berjalan berdasarkan periode.</p>
        </div>
        <a href="{{ route('reports.cash.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Ekspor / Cetak PDF
        </a>
    </div>

    {{-- ── Filter Form ── --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('reports.cash') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="start_date">
                    Dari Tanggal
                </label>
                <input id="start_date" type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5" for="end_date">
                    Hingga Tanggal
                </label>
                <input id="end_date" type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-colors">
                    Tampilkan
                </button>
                <a href="{{ route('reports.cash') }}"
                   class="px-4 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Reset
                </a>
            </div>
        </form>
        {{-- Active period badge --}}
        <div class="mt-3 flex items-center gap-2">
            <span class="period-badge">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Periode aktif: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
            </span>
            <span class="text-xs text-gray-400">({{ $transactions->count() }} transaksi)</span>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Saldo Awal --}}
        <div class="kpi-card kpi-slate rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-200">Saldo Awal</p>
                    <h2 class="mt-2 text-xl font-black leading-tight">
                        Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                    </h2>
                    <p class="mt-1 text-xs text-slate-300">
                        Sebelum {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Kas Masuk --}}
        <div class="kpi-card kpi-emerald rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-100">Kas Masuk (Debit)</p>
                    <h2 class="mt-2 text-xl font-black leading-tight">
                        + Rp {{ number_format($kasMasuk, 0, ',', '.') }}
                    </h2>
                    <p class="mt-1 text-xs text-emerald-200">Total pemasukan periode ini</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Kas Keluar --}}
        <div class="kpi-card kpi-rose rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-100">Kas Keluar (Kredit)</p>
                    <h2 class="mt-2 text-xl font-black leading-tight">
                        - Rp {{ number_format($kasKeluar, 0, ',', '.') }}
                    </h2>
                    <p class="mt-1 text-xs text-rose-200">Total pengeluaran periode ini</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Saldo Akhir --}}
        <div class="kpi-card kpi-blue rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-100">Saldo Akhir</p>
                    <h2 class="mt-2 text-xl font-black leading-tight">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </h2>
                    <p class="mt-1 text-xs text-blue-200">
                        Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Chart: Kas Masuk vs Kas Keluar ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-base font-bold text-gray-900">Kas Masuk vs Kas Keluar</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Distribusi harian / bulanan dalam periode yang dipilih
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>Kas Masuk
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>Kas Keluar
                </span>
            </div>
        </div>
        <div class="relative w-full" style="height:260px">
            <canvas id="cashFlowChart"></canvas>
        </div>
    </div>

    {{-- ── Transaction Statement Table ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Rincian Buku Kas (Rekening Koran)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Urutan kronologis dari awal periode</p>
            </div>
            <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                {{ $transactions->count() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold">Keterangan</th>
                        <th class="px-5 py-3 font-semibold">Referensi</th>
                        <th class="px-5 py-3 font-semibold">Metode</th>
                        <th class="px-5 py-3 font-semibold text-right">Tipe</th>
                        <th class="px-5 py-3 font-semibold text-right text-emerald-700">Debit (+)</th>
                        <th class="px-5 py-3 font-semibold text-right text-rose-700">Kredit (-)</th>
                        <th class="px-5 py-3 font-semibold text-right text-blue-700">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    {{-- ── Saldo Awal Row ── --}}
                    <tr class="bg-slate-50">
                        <td colspan="5" class="px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wide">
                            Saldo sebelum {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-gray-400">—</td>
                        <td class="px-5 py-3 text-right text-xs text-gray-400">—</td>
                        <td class="px-5 py-3 text-right font-bold text-slate-700">
                            Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                        </td>
                    </tr>

                    @php $runningBalance = $saldoAwal; @endphp

                    @forelse($transactions as $tx)
                        @php
                            $runningBalance += ($tx->type === 'debit') ? $tx->amount : -$tx->amount;
                            $typeParts = $tx->referenceable_type ? explode('\\', $tx->referenceable_type) : [];
                            $refLabel  = $tx->referenceable_type
                                ? (end($typeParts) . ' #' . $tx->referenceable_id)
                                : 'Manual';
                        @endphp
                        <tr class="tx-row hover:bg-gray-50/80">
                            {{-- Tanggal --}}
                            <td class="px-5 py-3.5 text-gray-500 text-xs whitespace-nowrap">
                                {{ $tx->transaction_date->translatedFormat('d M Y') }}
                            </td>
                            {{-- Keterangan --}}
                            <td class="px-5 py-3.5 text-gray-800 font-medium max-w-xs">
                                {{ $tx->description ?? '—' }}
                            </td>
                            {{-- Referensi --}}
                            <td class="px-5 py-3.5 text-gray-400 text-xs">
                                {{ $refLabel }}
                            </td>
                            {{-- Metode --}}
                            <td class="px-5 py-3.5 text-gray-500 text-xs">
                                {{ $tx->paymentMethod->name ?? '—' }}
                            </td>
                            {{-- Tipe badge --}}
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full
                                    {{ $tx->type === 'debit' ? 'badge-debit' : 'badge-credit' }}">
                                    {{ $tx->type === 'debit' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            {{-- Debit --}}
                            <td class="px-5 py-3.5 text-right text-emerald-600 font-semibold tabular-nums">
                                @if($tx->type === 'debit')
                                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            {{-- Kredit --}}
                            <td class="px-5 py-3.5 text-right text-rose-600 font-semibold tabular-nums">
                                @if($tx->type === 'credit')
                                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            {{-- Running Saldo --}}
                            <td class="px-5 py-3.5 text-right font-bold tabular-nums
                                {{ $runningBalance >= 0 ? 'saldo-positive' : 'saldo-negative' }}">
                                Rp {{ number_format($runningBalance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Tidak ada transaksi kas pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    {{-- ── Saldo Akhir Summary Row ── --}}
                    @if($transactions->isNotEmpty())
                    <tr class="bg-blue-50/60 border-t-2 border-blue-100">
                        <td colspan="5" class="px-5 py-3.5 font-bold text-blue-900 uppercase text-xs tracking-wide">
                            Saldo Akhir Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-emerald-700 tabular-nums">
                            Rp {{ number_format($kasMasuk, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-rose-700 tabular-nums">
                            Rp {{ number_format($kasKeluar, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-black text-blue-700 text-base tabular-nums">
                            Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels  = @json($chartDays);
    const masuk   = @json($chartMasuk);
    const keluar  = @json($chartKeluar);

    const ctx = document.getElementById('cashFlowChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label          : 'Kas Masuk',
                    data           : masuk,
                    backgroundColor: 'rgba(16, 185, 129, 0.80)',
                    borderRadius   : 4,
                    borderSkipped  : false,
                    order          : 2,
                },
                {
                    label          : 'Kas Keluar',
                    data           : keluar,
                    backgroundColor: 'rgba(244, 63, 94, 0.80)',
                    borderRadius   : 4,
                    borderSkipped  : false,
                    order          : 1,
                },
            ]
        },
        options: {
            responsive         : true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor     : '#94a3b8',
                    bodyColor      : '#f1f5f9',
                    padding        : 12,
                    cornerRadius   : 10,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': Rp ' +
                            new Intl.NumberFormat('id-ID').format(ctx.parsed.y),
                    }
                }
            },
            scales: {
                x: {
                    grid  : { display: false },
                    border: { display: false },
                    ticks : {
                        font    : { size: 10, family: 'Inter, sans-serif' },
                        color   : '#94a3b8',
                        maxTicksLimit: 15,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid       : { color: 'rgba(241,245,249,1)' },
                    border     : { display: false },
                    ticks      : {
                        font    : { size: 10, family: 'Inter, sans-serif' },
                        color   : '#94a3b8',
                        callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v),
                    }
                }
            }
        }
    });
});
</script>
@endpush
