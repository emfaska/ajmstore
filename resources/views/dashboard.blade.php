@extends('layouts.app')

@section('title', 'Dashboard Analitik — AJM Store')

@push('styles')
<style>
    /* ── Gradient backgrounds ── */
    .kpi-blue   { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); }
    .kpi-indigo { background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); }
    .kpi-rose   { background: linear-gradient(135deg, #be123c 0%, #f43f5e 100%); }
    .kpi-emerald{ background: linear-gradient(135deg, #047857 0%, #10b981 100%); }

    /* ── KPI card shimmer animation ── */
    @keyframes shimmer {
        0%   { opacity: .85; }
        50%  { opacity: 1;   }
        100% { opacity: .85; }
    }
    .kpi-card { animation: shimmer 3s ease-in-out infinite; }
    .kpi-card:hover { animation: none; transform: translateY(-3px); }

    /* ── Stat mini-cards ── */
    .stat-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,.12);
    }

    /* ── Table rows ── */
    .tx-row { transition: background .15s ease; }

    /* ── Badge colours ── */
    .badge-completed { background:#d1fae5; color:#065f46; }
    .badge-pending   { background:#fef3c7; color:#92400e; }
    .badge-cancelled { background:#fee2e2; color:#991b1b; }
    .badge-paid      { background:#dbeafe; color:#1e40af; }
    .badge-unpaid    { background:#fce7f3; color:#9d174d; }
    .badge-bengkel   { background:#ede9fe; color:#4c1d95; }
    .badge-umum      { background:#d1fae5; color:#065f46; }

    /* ── Chart wrapper ── */
    #salesTrendChart { max-height: 320px; }

    /* ── Low-stock pulse ── */
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0   rgba(245,158,11,.55); }
        70%  { box-shadow: 0 0 0 8px rgba(245,158,11,0);   }
        100% { box-shadow: 0 0 0 0   rgba(245,158,11,0);   }
    }
    .pulse-amber { animation: pulse-ring 2.2s ease infinite; }
</style>
@endpush

@section('content')
<div class="space-y-7">

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Dashboard Analitik</h1>
            <p class="mt-1 text-sm text-gray-500">
                Ringkasan performa finansial & transaksi AJM Store · Per {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
        {{-- Live clock badge --}}
        <div id="liveTime" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm self-start sm:self-auto">
            <span class="w-2 h-2 bg-emerald-400 rounded-full inline-block animate-pulse"></span>
            <span id="clockText"></span>
        </div>
    </div>

    {{-- ── KPI Cards (4 columns) ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Total Penjualan --}}
        <div class="kpi-card kpi-blue rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-100">Total Penjualan</p>
                    <h2 class="mt-2 text-2xl font-black leading-none">Rp {{ number_format($totalSales, 0, ',', '.') }}</h2>
                    <p class="mt-1.5 text-xs text-blue-200">Semua transaksi selesai</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-1.5 text-xs text-blue-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Hari ini: <strong class="text-white">Rp {{ number_format($salesToday, 0, ',', '.') }}</strong>
            </div>
        </div>

        {{-- Total Pembelian --}}
        <div class="kpi-card kpi-indigo rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-indigo-100">Total Pembelian</p>
                    <h2 class="mt-2 text-2xl font-black leading-none">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</h2>
                    <p class="mt-1.5 text-xs text-indigo-200">Barang dari supplier</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-1.5 text-xs text-indigo-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Status: <strong class="text-white">Completed only</strong>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="kpi-card kpi-rose rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-100">Total Pengeluaran</p>
                    <h2 class="mt-2 text-2xl font-black leading-none">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h2>
                    <p class="mt-1.5 text-xs text-rose-200">Biaya operasional toko</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-1.5 text-xs text-rose-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pengeluaran non-pembelian
            </div>
        </div>

        {{-- Saldo Kas --}}
        <div class="kpi-card kpi-emerald rounded-2xl p-5 text-white shadow-lg cursor-default">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-100">Saldo Kas Saat Ini</p>
                    <h2 class="mt-2 text-2xl font-black leading-none">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h2>
                    <p class="mt-1.5 text-xs text-emerald-200">Debit – Kredit</p>
                </div>
                <div class="bg-white/20 rounded-xl p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-1.5 text-xs text-emerald-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Saldo kas aktif
            </div>
        </div>
    </div>

    {{-- ── Second Row: Produk Stats + Chart ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Produk mini-cards (stack) --}}
        <div class="flex flex-col gap-5">
            {{-- Jumlah Produk --}}
            <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Jumlah Produk</p>
                    <p class="text-3xl font-black text-gray-900 mt-0.5">{{ number_format($totalProducts) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Produk aktif di sistem</p>
                </div>
            </div>

            {{-- Stok Menipis --}}
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}"
               class="stat-card bg-amber-50 border border-amber-200 rounded-2xl shadow-sm p-5 flex items-center gap-4 group">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors
                            {{ $lowStockCount > 0 ? 'pulse-amber' : '' }}">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-widest">Stok Menipis</p>
                    <p class="text-3xl font-black {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-0.5">
                        {{ number_format($lowStockCount) }}
                    </p>
                    <p class="text-xs text-amber-500 mt-0.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Klik untuk lihat produk
                    </p>
                </div>
            </a>
        </div>

        {{-- Sales Trend Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Grafik Penjualan</h3>
                    <p class="text-xs text-gray-400 mt-0.5">30 hari terakhir (transaksi selesai)</p>
                </div>
                <span class="text-xs font-semibold bg-blue-50 text-blue-600 px-3 py-1 rounded-full">
                    Chart.js
                </span>
            </div>
            <div class="relative w-full" style="height:280px">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Latest 10 Transactions ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Table header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">10 Transaksi Terbaru</h3>
                <p class="text-xs text-gray-400 mt-0.5">Penjualan paling baru di sistem</p>
            </div>
            <a href="{{ route('sales.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                Lihat Semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3 font-semibold">Invoice</th>
                        <th class="px-6 py-3 font-semibold">Tipe</th>
                        <th class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold text-right">Total</th>
                        <th class="px-6 py-3 font-semibold text-center">Status</th>
                        <th class="px-6 py-3 font-semibold text-center">Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($latestTransactions as $tx)
                        <tr class="tx-row hover:bg-gray-50/80 transition-colors">
                            {{-- Invoice --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $tx->id) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800 hover:underline text-sm">
                                    {{ $tx->invoice_number }}
                                </a>
                            </td>

                            {{-- Tipe --}}
                            <td class="px-6 py-4">
                                @if(($tx->transaction_type ?? '') === 'bengkel')
                                    <span class="badge-bengkel text-[11px] font-bold px-2 py-0.5 rounded-full">Bengkel</span>
                                @else
                                    <span class="badge-umum text-[11px] font-bold px-2 py-0.5 rounded-full">Umum</span>
                                @endif
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                {{ $tx->customer->name ?? '— Umum —' }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $tx->sale_date->translatedFormat('d M Y') }}
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($tx->status === 'completed')
                                    <span class="badge-completed text-[11px] font-bold px-2.5 py-0.5 rounded-full">Selesai</span>
                                @elseif($tx->status === 'pending')
                                    <span class="badge-pending text-[11px] font-bold px-2.5 py-0.5 rounded-full">Pending</span>
                                @else
                                    <span class="badge-cancelled text-[11px] font-bold px-2.5 py-0.5 rounded-full">Batal</span>
                                @endif
                            </td>

                            {{-- Pembayaran --}}
                            <td class="px-6 py-4 text-center">
                                @if($tx->payment_status === 'paid')
                                    <span class="badge-paid text-[11px] font-bold px-2.5 py-0.5 rounded-full">Lunas</span>
                                @else
                                    <span class="badge-unpaid text-[11px] font-bold px-2.5 py-0.5 rounded-full">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada transaksi penjualan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Live Clock ──────────────────────────────────────────────────────────
    function updateClock() {
        const now    = new Date();
        const opts   = { weekday:'short', hour:'2-digit', minute:'2-digit', second:'2-digit' };
        document.getElementById('clockText').textContent =
            now.toLocaleTimeString('id-ID', opts);
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Sales Trend Chart ────────────────────────────────────────────────────
    const labels  = @json($chartLabels);
    const values  = @json($chartData);

    const ctx = document.getElementById('salesTrendChart').getContext('2d');

    // Gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0,   'rgba(59,130,246,0.30)');
    gradient.addColorStop(1,   'rgba(59,130,246,0.00)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label      : 'Penjualan (Rp)',
                data       : values,
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth     : 2.5,
                fill            : true,
                tension         : 0.4,
                pointRadius     : 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor    : '#fff',
                pointBorderWidth    : 2,
            }]
        },
        options: {
            responsive        : true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor : '#1e293b',
                    titleColor      : '#94a3b8',
                    bodyColor       : '#f1f5f9',
                    padding         : 12,
                    cornerRadius    : 10,
                    displayColors   : false,
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y),
                    }
                }
            },
            scales: {
                x: {
                    grid : { display: false },
                    ticks: {
                        font    : { size: 10, family: 'Inter, sans-serif' },
                        color   : '#94a3b8',
                        maxTicksLimit: 10,
                    },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid       : { color: 'rgba(241,245,249,1)', lineWidth: 1 },
                    ticks: {
                        font    : { size: 10, family: 'Inter, sans-serif' },
                        color   : '#94a3b8',
                        callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation:'compact' }).format(v),
                    },
                    border: { display: false },
                }
            }
        }
    });
});
</script>
@endpush
