@extends('layouts.app')

@section('title', 'Laporan Pembelian - AJM Store')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Pembelian Barang</h2>
            <p class="text-sm text-gray-500">Monitor pembelanjaan barang, biaya masuk, dan pengeluaran kas aktual ke supplier.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Export Excel -->
            <a href="{{ route('reports.purchases.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'supplier_id' => $supplier_id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg shadow hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel
            </a>
            <!-- Export PDF -->
            <a href="{{ route('reports.purchases.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'supplier_id' => $supplier_id]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Ekspor PDF (Cetak)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.purchases') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
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

            <!-- Supplier -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Supplier</label>
                <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ (string) $supplier_id === (string) $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('reports.purchases') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <!-- Total Pembelian -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-650 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pembelian (Invoice)</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($totalPembelian, 2, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Pengeluaran Pembelian -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-650 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Pengeluaran Kas (Terbayar)</span>
                <h3 class="text-lg font-bold text-red-650 mt-0.5">Rp {{ number_format($totalPengeluaran, 2, ',', '.') }}</h3>
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
                        <th scope="col" class="px-6 py-4">Tanggal Pembelian</th>
                        <th scope="col" class="px-6 py-4">Supplier</th>
                        <th scope="col" class="px-6 py-4 text-center">Status Transaksi</th>
                        <th scope="col" class="px-6 py-4 text-center">Status Pembayaran</th>
                        <th scope="col" class="px-6 py-4 text-right">Total Pembelian</th>
                        <th scope="col" class="px-6 py-4 text-right">Total Terbayar (Kas Keluar)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        @php
                            $totalPaid = $purchase->cashTransactions->where('type', 'credit')->sum('amount');
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <!-- Invoice -->
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="hover:underline text-blue-600">
                                    {{ $purchase->invoice_number }}
                                </a>
                            </td>
                            <!-- Tanggal -->
                            <td class="px-6 py-4">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <!-- Supplier -->
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $purchase->supplier->name ?? 'N/A' }}
                            </td>
                            <!-- Status Transaksi -->
                            <td class="px-6 py-4 text-center">
                                @if($purchase->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">Completed</span>
                                @elseif($purchase->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <!-- Status Pembayaran -->
                            <td class="px-6 py-4 text-center">
                                @if($purchase->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800">Unpaid</span>
                                @endif
                            </td>
                            <!-- Total Pembelian -->
                            <td class="px-6 py-4 text-right font-bold text-gray-950">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <!-- Total Terbayar -->
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada transaksi pembelian yang cocok dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
