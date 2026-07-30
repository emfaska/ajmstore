@extends('layouts.app')

@section('title', 'Detail Transaksi - AJM Store')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumbs / Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Penjualan
        </a>
        <div class="flex gap-2">
            <a href="{{ route('sales.create') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Transaksi Baru (POS)
            </a>
            <a href="{{ route('sales.print', $sale->id) . (session('cash_received') ? '?cash_received='.session('cash_received').'&change='.session('change') : '') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Struk
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl text-sm text-green-800 bg-green-50 border border-green-200 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Invoice Header -->
        <div class="p-6 bg-gray-50 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Faktur Penjualan</span>
                <h2 class="text-2xl font-black text-gray-800">{{ $sale->invoice_number }}</h2>
                <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                    <span>Tanggal: <strong>{{ $sale->sale_date->format('d F Y') }}</strong></span>
                    <span>Tipe Transaksi: 
                        <strong class="capitalize">{{ str_replace('_', ' ', $sale->transaction_type ?? 'penjualan_umum') }}</strong>
                    </span>
                </div>
            </div>
            <div class="flex flex-col md:items-end justify-between">
                <div class="flex gap-2">
                    <!-- Status Transaksi -->
                    @if($sale->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Selesai (Completed)</span>
                    @elseif($sale->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending (Draft)</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Cancelled (Batal)</span>
                    @endif

                    <!-- Status Pembayaran -->
                    @if($sale->payment_status === 'paid')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Lunas (Paid)</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Belum Lunas (Unpaid)</span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-2 md:mt-0">
                    Metode Pembayaran: <strong class="text-gray-800">{{ $sale->paymentMethod->name }}</strong>
                </div>
            </div>
        </div>

        <!-- Info Grid (Customer, Vehicle details) -->
        <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-100 bg-gray-50/20">
            <!-- Customer Section -->
            <div class="p-6 border-b md:border-b-0 md:border-r border-gray-100 space-y-2">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Pelanggan</h4>
                @if($sale->customer)
                    <div class="space-y-1 text-sm">
                        <p class="font-bold text-gray-800">{{ $sale->customer->name }}</p>
                        <p class="text-gray-600">Telp: {{ $sale->customer->phone ?? '-' }}</p>
                        <p class="text-gray-600">Alamat: {{ $sale->customer->address ?? '-' }}</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Pelanggan Umum (Tanpa Nama)</p>
                @endif
            </div>

            <!-- Vehicle Section (For Bengkel) -->
            <div class="p-6 space-y-2">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Kendaraan</h4>
                @if($sale->vehicle)
                    <div class="space-y-1 text-sm">
                        <p class="font-bold text-gray-850">No. Polisi: <span class="bg-slate-100 border px-1.5 py-0.5 rounded font-mono text-slate-800 font-bold">{{ $sale->vehicle->license_plate }}</span></p>
                        <p class="text-gray-600">Merek/Model: {{ $sale->vehicle->brand }} {{ $sale->vehicle->model }}</p>
                        <p class="text-gray-600">Tahun/Warna: {{ $sale->vehicle->year ?? '-' }} / {{ $sale->vehicle->color ?? '-' }}</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada kendaraan (Penjualan Umum)</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6 space-y-4">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Item Barang Keluar</h4>
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">Nama Produk</th>
                            <th scope="col" class="px-4 py-3">SKU</th>
                            <th scope="col" class="px-4 py-3 text-center">Qty</th>
                            <th scope="col" class="px-4 py-3 text-right">Harga Jual</th>
                            <th scope="col" class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50/50">
                                <td class="px-4 py-3.5 font-semibold text-gray-800">
                                    {{ $item->product->name }}
                                </td>
                                <td class="px-4 py-3.5 text-gray-500">
                                    {{ $item->product->sku ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-medium text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-800">
                                    Rp {{ number_format($item->selling_price, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-gray-900">
                                    Rp {{ number_format($item->subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Notes and Calculations -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notes -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keterangan / Catatan</h4>
                <div class="p-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 min-h-[80px]">
                    {{ $sale->notes ?: 'Tidak ada catatan untuk transaksi ini.' }}
                </div>
            </div>

            <!-- Calculations Summary -->
            <div class="space-y-3">
                <div class="space-y-1.5 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($sale->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Diskon</span>
                        <span class="font-semibold text-red-600">- Rp {{ number_format($sale->discount, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pajak (PPN)</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($sale->tax, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-extrabold text-gray-900 border-t border-gray-200 pt-2.5">
                        <span>Grand Total</span>
                        <span class="text-blue-600 text-lg">Rp {{ number_format($sale->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Cash Payment info if available in session -->
                @if(session('cash_received') || request('cash_received'))
                    @php
                        $cashReceived = session('cash_received', request('cash_received'));
                        $change = session('change', request('change'));
                    @endphp
                    <div class="mt-4 p-3 bg-blue-50/50 rounded-xl border border-blue-100 space-y-1.5 text-xs text-blue-900 font-medium">
                        <div class="flex justify-between">
                            <span>Uang Diterima:</span>
                            <span class="font-bold">Rp {{ number_format($cashReceived, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-blue-200/50 pt-1.5 text-sm">
                            <span class="font-bold">Kembalian:</span>
                            <span class="font-extrabold text-green-600">Rp {{ number_format($change, 2, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
