@extends('layouts.app')

@section('title', 'Dashboard Analitik - AJM Store')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Analitik</h2>
        <p class="text-sm text-gray-500">Ringkasan performa finansial dan transaksi toko AJM Store.</p>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Penjualan -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Penjualan</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Pembelian -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-55 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pembelian</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pengeluaran</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Saldo Kas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Saldo Kas Saat Ini</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Product Stats & Stock warning -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Jumlah Produk -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Jumlah Produk</span>
                <h3 class="text-2xl font-black text-gray-800">{{ $totalProducts }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>

        <!-- Stok Menipis -->
        <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 hover:border-amber-400 shadow-sm flex justify-between items-center transition-colors">
            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Produk Stok Menipis</span>
                <h3 class="text-2xl font-black text-amber-600">{{ $lowStockCount }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </a>
    </div>

    <!-- Chart & Sales Trend -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 mb-4">Grafik Penjualan 30 Hari Terakhir</h3>
        <div class="relative w-full h-80">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <!-- Latest Transactions (Sales) -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">10 Transaksi Terbaru (Penjualan)</h3>
            <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Invoice</th>
                        <th scope="col" class="px-6 py-4">Tipe</th>
                        <th scope="col" class="px-6 py-4">Pelanggan</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Total</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestTransactions as $tx)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <a href="{{ route('sales.show', $tx->id) }}" class="hover:underline text-blue-600">
                                    {{ $tx->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if(($tx->transaction_type ?? '') === 'bengkel')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">Bengkel</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $tx->customer->name ?? 'Pelanggan Umum' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $tx->sale_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-950">
                                Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tx->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">Completed</span>
                                @elseif($tx->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tx->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Belum ada transaksi penjualan tercatat.
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Total Penjualan (Rupiah)',
                    data: @json($chartData),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.parsed.y;
                                return ' Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(243, 244, 246, 1)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
