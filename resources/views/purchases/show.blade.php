@extends('layouts.app')

@section('page_title', 'Detail Transaksi Pembelian')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('purchases.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Transaksi Pembelian</h2>
            <p class="text-sm text-gray-500 font-normal">Informasi lengkap transaksi pembelian barang masuk.</p>
        </div>
    </div>

    <!-- Purchase Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Info -->
        <div class="p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">No. Invoice</span>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5">{{ $purchase->invoice_number }}</h3>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <!-- Status Transaksi -->
                    @if($purchase->status === 'completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Completed</span>
                    @elseif($purchase->status === 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending (Draft)</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                    @endif
                    
                    <!-- Status Pembayaran -->
                    @if($purchase->payment_status === 'paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Paid (Lunas)</span>
                    @elseif($purchase->payment_status === 'partially_paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Partially Paid</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Unpaid (Belum Lunas)</span>
                    @endif

                    @if($purchase->trashed())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terhapus</span>
                    @endif
                </div>
            </div>

            <!-- Edit Button if eligible -->
            @if(!$purchase->trashed() && $purchase->status === 'pending' && $purchase->payment_status !== 'paid')
                <a href="{{ route('purchases.edit', $purchase->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg shadow hover:bg-yellow-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Ubah Transaksi
                </a>
            @endif
        </div>

        <!-- Details Data -->
        <div class="p-8 space-y-8">
            <div>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Informasi Pemasok & Waktu</h4>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Supplier / Perusahaan</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $purchase->supplier->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Transaksi</dt>
                        <dd class="mt-1 text-base font-medium text-gray-900">{{ $purchase->purchase_date->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kontak Supplier</dt>
                        <dd class="mt-1 text-sm text-gray-700">
                            PIC: {{ $purchase->supplier->pic_name ?? '-' }} <br>
                            Telp: {{ $purchase->supplier->phone ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Input Data</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $purchase->created_at->translatedFormat('d F Y, H:i') }} WIB</dd>
                    </div>
                </dl>
            </div>

            <!-- Items list -->
            <div>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Daftar Barang yang Dibeli</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nama Produk</th>
                                <th scope="col" class="px-4 py-3 text-center">Jumlah (Qty)</th>
                                <th scope="col" class="px-4 py-3 text-right">Harga Beli</th>
                                <th scope="col" class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                                <tr class="border-b border-gray-100 bg-white">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $item->product->name }}
                                        <p class="text-xs text-gray-400 mt-0.5">Barcode: {{ $item->product->barcode ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-800">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-800">
                                        Rp {{ number_format($item->cost_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-950">
                                        Rp {{ number_format($item->subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Sum row -->
                            <tr class="bg-gray-50 font-semibold text-gray-900">
                                <td colspan="3" class="px-4 py-4 text-right">Total Transaksi:</td>
                                <td class="px-4 py-4 text-right text-base text-blue-600 font-bold">
                                    Rp {{ number_format($purchase->total_amount, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment history -->
            <div>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Riwayat Pembayaran Kas Keluar</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Tanggal Transaksi</th>
                                <th scope="col" class="px-4 py-3">Metode Pembayaran</th>
                                <th scope="col" class="px-4 py-3">Keterangan</th>
                                <th scope="col" class="px-4 py-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->cashTransactions as $tx)
                                <tr class="border-b border-gray-100 bg-white">
                                    <td class="px-4 py-3 text-gray-800">
                                        {{ $tx->transaction_date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $tx->paymentMethod->name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $tx->description }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-red-600">
                                        - Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                        Tidak ada catatan transaksi kas masuk/keluar untuk invoice ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
