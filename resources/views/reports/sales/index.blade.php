@extends('layouts.app')

@section('title', 'Laporan Penjualan - AJM Store')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Penjualan</h2>
            <p class="text-sm text-gray-500">Analisis omzet, volume produk terjual, dan profitabilitas (laba bersih) transaksi penjualan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Export Excel -->
            <a href="{{ route('reports.sales.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'transaction_type' => $transaction_type]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg shadow hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel
            </a>
            <!-- Export PDF -->
            <a href="{{ route('reports.sales.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'transaction_type' => $transaction_type]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Ekspor PDF (Cetak)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <!-- Dari Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Hingga Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Hingga Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                <select name="transaction_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="penjualan_umum" {{ $transaction_type === 'penjualan_umum' ? 'selected' : '' }}>Penjualan Umum</option>
                    <option value="bengkel" {{ $transaction_type === 'bengkel' ? 'selected' : '' }}>Bengkel</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('reports.sales') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Total Omzet -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Omzet</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($totalOmzet, 2, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Item Terjual -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Item Terjual</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">{{ number_format($totalItemTerjual, 0, ',', '.') }} pcs</h3>
            </div>
        </div>

        <!-- Total Laba Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Estimasi Laba Bersih</span>
                <h3 class="text-lg font-bold text-emerald-600 mt-0.5">Rp {{ number_format($totalLaba, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800">Daftar Transaksi Terfilter</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">No. Invoice</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Jenis</th>
                        <th scope="col" class="px-6 py-4">Pelanggan</th>
                        <th scope="col" class="px-6 py-4 text-right">Subtotal</th>
                        <th scope="col" class="px-6 py-4 text-right">Diskon</th>
                        <th scope="col" class="px-6 py-4 text-right">Pajak</th>
                        <th scope="col" class="px-6 py-4 text-right">Total Omzet</th>
                        <th scope="col" class="px-6 py-4 text-center">Item Terjual</th>
                        <th scope="col" class="px-6 py-4 text-right">Laba Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $qtySold = $sale->items->sum('quantity');
                            
                            // Laba bersih invoice = sum of items laba - invoice discount
                            $saleLaba = 0;
                            foreach($sale->items as $item) {
                                $purchasePrice = (float) ($item->product->purchase_price ?? 0);
                                $saleLaba += ($item->selling_price - $purchasePrice) * $item->quantity;
                            }
                            $saleLaba -= $sale->discount;
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <!-- Invoice -->
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <a href="{{ route('sales.show', $sale->id) }}" class="hover:underline text-blue-600">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <!-- Tanggal -->
                            <td class="px-6 py-4">
                                {{ $sale->sale_date->format('d/m/Y') }}
                            </td>
                            <!-- Jenis -->
                            <td class="px-6 py-4">
                                @if($sale->transaction_type === 'bengkel')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">Bengkel</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Umum</span>
                                @endif
                            </td>
                            <!-- Pelanggan -->
                            <td class="px-6 py-4 text-gray-700">
                                {{ $sale->customer->name ?? 'Pelanggan Umum' }}
                            </td>
                            <!-- Subtotal -->
                            <td class="px-6 py-4 text-right">
                                Rp {{ number_format($sale->subtotal, 0, ',', '.') }}
                            </td>
                            <!-- Diskon -->
                            <td class="px-6 py-4 text-right text-red-600">
                                {{ $sale->discount > 0 ? '- Rp ' . number_format($sale->discount, 0, ',', '.') : '-' }}
                            </td>
                            <!-- Pajak -->
                            <td class="px-6 py-4 text-right">
                                {{ $sale->tax > 0 ? 'Rp ' . number_format($sale->tax, 0, ',', '.') : '-' }}
                            </td>
                            <!-- Grand Total -->
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                            <!-- Qty Sold -->
                            <td class="px-6 py-4 text-center font-medium">
                                {{ $qtySold }} pcs
                            </td>
                            <!-- Laba -->
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                Rp {{ number_format($saleLaba, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada transaksi penjualan yang cocok dengan filter tanggal/jenis ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
