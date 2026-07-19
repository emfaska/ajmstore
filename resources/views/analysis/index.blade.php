@extends('layouts.app')

@section('title', 'Analisis Penjualan - AJM Store')
@section('page_title', 'Analisis Penjualan')

@section('content')
<div class="space-y-8">
    <!-- Header Page & Date Range Form Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-fade-in">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Dashboard Analisis Penjualan</h2>
            <p class="text-sm text-slate-500 mt-1">Pantau perkembangan bisnis Anda melalui laporan penjualan terperinci.</p>
        </div>
        
        <form action="{{ route('analysis.index') }}" method="GET" class="flex flex-wrap items-end gap-3 lg:justify-end">
            <div class="w-full sm:w-auto">
                <label for="start_date" class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}"
                       class="w-full sm:w-auto px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-medium @error('start_date') border-red-500 ring-2 ring-red-100 @enderror">
            </div>
            
            <div class="w-full sm:w-auto">
                <label for="end_date" class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}"
                       class="w-full sm:w-auto px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-medium @error('end_date') border-red-500 ring-2 ring-red-100 @enderror">
            </div>
            
            <button type="submit" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-all shadow-md shadow-brand-500/10 hover:shadow-brand-500/20 active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter
            </button>

            @if(request('start_date') || request('end_date'))
                <a href="{{ route('analysis.index') }}" class="w-full sm:w-auto text-slate-500 hover:text-slate-700 hover:bg-slate-100 border border-slate-200 font-semibold text-sm px-4 py-2.5 rounded-xl transition-all active:scale-95 flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Error Validation Display -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h3 class="font-bold text-red-800 text-sm">Gagal Menyaring Data</h3>
                <ul class="list-disc list-inside text-xs text-red-700 mt-1 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Cards Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-brand-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight relative z-10">
                Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-emerald-600 font-semibold mt-2 relative z-10 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Status Pesanan Selesai
            </p>
        </div>

        <!-- Orders Card -->
        <div class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-teal-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight relative z-10">
                {{ number_format($summary['total_orders'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-teal-600 font-semibold mt-2 relative z-10 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-teal-500 rounded-full"></span> Jumlah Invoice Terbayar
            </p>
        </div>

        <!-- AOV Card -->
        <div class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Nilai Keranjang</span>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight relative z-10">
                Rp {{ number_format($summary['average_order_value'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-amber-600 font-semibold mt-2 relative z-10 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Average Order Value (AOV)
            </p>
        </div>

        <!-- Items Sold Card -->
        <div class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-violet-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Barang Terjual</span>
                <div class="w-10 h-10 bg-violet-50 text-violet-600 rounded-xl flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight relative z-10">
                {{ number_format($summary['total_items_sold'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-violet-600 font-semibold mt-2 relative z-10 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full"></span> Kuantitas Produk Terjual
            </p>
        </div>
    </div>

    <!-- Charts Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sales Trend Line Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Grafik Tren Penjualan Harian</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pendapatan harian berdasarkan tanggal order selesai</p>
                </div>
                <div class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100 flex items-center gap-1">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span> Live Data
                </div>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Status Order Donut Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Distribusi Status Pesanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Persentase status transaksi pada rentang tanggal</p>
            </div>
            <div class="h-64 w-full relative flex items-center justify-center my-4">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs mt-2 pt-2 border-t border-slate-100">
                <div>
                    <span class="block font-bold text-emerald-600">{{ $status_distribution['completed'] }}</span>
                    <span class="text-slate-400 font-medium">Selesai</span>
                </div>
                <div>
                    <span class="block font-bold text-blue-600">{{ $status_distribution['pending'] }}</span>
                    <span class="text-slate-400 font-medium">Pending</span>
                </div>
                <div>
                    <span class="block font-bold text-red-600">{{ $status_distribution['cancelled'] }}</span>
                    <span class="text-slate-400 font-medium">Batal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Leaderboard Table & Summary -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-lg">Peringkat 5 Produk Terlaris</h3>
            <p class="text-xs text-slate-400 mt-0.5">Produk paling diminati oleh pelanggan berdasarkan kuantitas terjual.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 text-xs uppercase">
                        <th class="px-6 py-4">No.</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4 text-center">Kuantitas Terjual</th>
                        <th class="px-6 py-4 text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($top_products as $index => $product)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400">
                                @if($index == 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-700 font-bold text-xs">🥇</span>
                                @elseif($index == 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-xs">🥈</span>
                                @elseif($index == 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-700 font-bold text-xs">🥉</span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $product['name'] }}</td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $product['sku'] }}</td>
                            <td class="px-6 py-4 text-center text-slate-900 font-extrabold">{{ number_format($product['total_quantity'], 0, ',', '.') }} unit</td>
                            <td class="px-6 py-4 text-right text-brand-600 font-extrabold">Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada data penjualan pada rentang tanggal ini</p>
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
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data for Sales Trend
        const trendData = @json($trends);
        const dates = trendData.map(item => {
            const dateObj = new Date(item.date);
            return dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        });
        const revenues = trendData.map(item => item.revenue);

        // Render Sales Trend Chart
        const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
        
        // Gradient for line chart fill
        const brandGradient = trendCtx.createLinearGradient(0, 0, 0, 300);
        brandGradient.addColorStop(0, 'rgba(82, 117, 255, 0.35)');
        brandGradient.addColorStop(1, 'rgba(82, 117, 255, 0.00)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: revenues,
                        borderColor: '#5275ff',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#5275ff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        fill: true,
                        backgroundColor: brandGradient,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: {
                            color: 'rgba(241, 245, 249, 0.8)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 10,
                                weight: '500'
                            },
                            callback: function(value) {
                                return 'Rp ' + (value >= 1e6 ? (value / 1e6) + 'jt' : (value >= 1e3 ? (value / 1e3) + 'k' : value));
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 10,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });

        // Data for Status Distribution Chart
        const statusDist = @json($status_distribution);
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending', 'Batal'],
                datasets: [{
                    data: [statusDist.completed, statusDist.pending, statusDist.cancelled],
                    backgroundColor: ['#10b981', '#3b82f6', '#ef4444'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                return ` ${context.label}: ${value} transaksi (${percentage})`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
