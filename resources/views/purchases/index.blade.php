@extends('layouts.app')

@section('page_title', 'Transaksi Pembelian')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Pembelian</h2>
            <p class="text-sm text-gray-500">Kelola transaksi pembelian barang masuk ke supplier.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pembelian
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
        <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">No. Invoice</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Supplier Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Supplier</label>
                <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Pembayaran</label>
                <select name="payment_status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
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
            <div class="lg:col-span-2 flex items-center gap-2">
                <label class="inline-flex items-center cursor-pointer mr-2 select-none">
                    <input type="checkbox" name="trashed" value="1" {{ request('trashed') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Sampah</span>
                </label>
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('purchases.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
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
                        <th scope="col" class="px-6 py-4">Supplier</th>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Total</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Pembayaran</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr class="bg-white border-b hover:bg-gray-50 {{ $purchase->trashed() ? 'opacity-70 bg-gray-100' : '' }}">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $purchase->invoice_number }}
                                @if($purchase->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-red-100 text-red-800">Terhapus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-950">
                                Rp {{ number_format($purchase->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($purchase->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                @elseif($purchase->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($purchase->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                @elseif($purchase->payment_status === 'partially_paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Partial</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($purchase->trashed())
                                        <!-- Restore Button -->
                                        <form action="{{ route('purchases.restore', $purchase->id) }}" method="POST" onsubmit="return confirm('Kembalikan transaksi pembelian ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        @if($purchase->status === 'pending' && $purchase->payment_status !== 'paid')
                                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                        @endif

                                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi pembelian ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <p class="text-base font-medium text-gray-900">Belum ada transaksi pembelian</p>
                                    <p class="text-sm mt-1">Gunakan tombol 'Tambah Pembelian' untuk membuat transaksi baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($purchases->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $purchases->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
