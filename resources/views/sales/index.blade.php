@extends('layouts.app')

@section('title', 'Daftar Penjualan - AJM Store')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Penjualan / Pengeluaran Barang</h2>
            <p class="text-sm text-gray-500">Kelola transaksi barang keluar untuk Bengkel maupun Penjualan Umum.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Halaman Kasir (POS)
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
            <!-- Search Keyword -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">No. Invoice</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status Transaksi</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Draft)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Hingga Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Action buttons -->
            <div class="lg:col-span-2 flex items-center gap-4">
                <label class="inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="trashed" value="1" {{ request('trashed') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Lihat Sampah</span>
                </label>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('sales.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">No. Invoice</th>
                        <th scope="col" class="px-6 py-4">Jenis Transaksi</th>
                        <th scope="col" class="px-6 py-4">Pelanggan</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Total</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Pembayaran</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="bg-white border-b hover:bg-gray-50 {{ $sale->trashed() ? 'opacity-70 bg-gray-100' : '' }}">
                            <!-- Invoice -->
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $sale->invoice_number }}
                                @if($sale->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-red-100 text-red-800">Terhapus</span>
                                @endif
                            </td>
                            <!-- Jenis Transaksi -->
                            <td class="px-6 py-4 font-medium text-gray-800">
                                @if(($sale->transaction_type ?? '') === 'bengkel')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-55 text-indigo-800 border border-indigo-200">
                                        🔧 Bengkel
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-55 text-emerald-800 border border-emerald-200">
                                        🛒 Penjualan Umum
                                    </span>
                                @endif
                            </td>
                            <!-- Pelanggan -->
                            <td class="px-6 py-4 text-gray-700">
                                {{ $sale->customer->name ?? 'Pelanggan Umum' }}
                            </td>
                            <!-- Tanggal -->
                            <td class="px-6 py-4 text-gray-600">
                                {{ $sale->sale_date->format('d/m/Y') }}
                            </td>
                            <!-- Total -->
                            <td class="px-6 py-4 font-bold text-gray-950">
                                Rp {{ number_format($sale->total_amount, 2, ',', '.') }}
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                @elseif($sale->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <!-- Pembayaran -->
                            <td class="px-6 py-4 text-center">
                                @if($sale->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid (Lunas)</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Unpaid (Belum Lunas)</span>
                                @endif
                            </td>
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($sale->trashed())
                                        <!-- Restore form -->
                                        <form action="{{ route('sales.restore', $sale->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-xs font-semibold">Pulihkan</button>
                                        </form>
                                    @else
                                        <!-- Detail (Show) -->
                                        <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:text-blue-900 text-xs font-semibold">Detail</a>
                                        
                                        <!-- Cetak Struk -->
                                        <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-900 text-xs font-semibold">Struk</a>
                                        
                                        <!-- Soft Delete -->
                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada data transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sales->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
