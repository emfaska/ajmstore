@extends('layouts.app')

@section('title', 'Laporan Laba Rugi — AJM Store')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Laporan Laba Rugi</h1>
                <p class="mt-1 text-sm text-gray-500">Ringkasan pendapatan dan pengeluaran per periode.</p>
            </div>
            <a href="{{ route('reports.profit_loss.pdf', ['month' => $month, 'year' => $year]) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg">
                Cetak PDF
            </a>
        </div>

        <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('reports.profit_loss') }}" class="flex gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500">Bulan</label>
                    <select name="month" class="mt-1 rounded-lg border-gray-200">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate($year, $m, 1)->translatedFormat('F') }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}"
                        class="mt-1 rounded-lg border-gray-200 w-28" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Tampilkan</button>
                    <a href="{{ route('reports.profit_loss') }}" class="px-4 py-2 bg-gray-100 rounded-lg">Reset</a>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Total Penjualan</p>
                    <p class="text-xl font-bold">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Pemasukan Lain</p>
                    <p class="text-xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Pendapatan</p>
                    <p class="text-xl font-bold">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Total Pembelian</p>
                    <p class="text-xl font-bold">Rp {{ number_format($totalPurchase, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Pengeluaran Operasional</p>
                    <p class="text-xl font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-white border rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">Pengeluaran</p>
                    <p class="text-xl font-bold">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-white border rounded-lg">
                <h3 class="text-lg font-bold">Laba Bersih</h3>
                <p class="text-2xl font-extrabold text-green-600">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p>
            </div>

            <div class="mt-6 bg-white p-4 rounded-lg border">
                <h4 class="font-bold mb-2">Grafik Bulanan</h4>
                <div style="height:260px">
                    <canvas id="plChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($chartLabels);
            const revenue = @json($chartDataRevenue);
            const expense = @json($chartDataExpense);
            const profit = @json($chartDataProfit);

            const ctx = document.getElementById('plChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Pendapatan',
                            data: revenue,
                            borderColor: '#10b981',
                            backgroundColor: '#d1fae5',
                            tension: 0.3
                        },
                        {
                            label: 'Pengeluaran',
                            data: expense,
                            borderColor: '#ef4444',
                            backgroundColor: '#fee2e2',
                            tension: 0.3
                        },
                        {
                            label: 'Laba',
                            data: profit,
                            borderColor: '#2563eb',
                            backgroundColor: '#dbeafe',
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endpush
