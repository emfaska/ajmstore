@extends('layouts.app')

@section('page_title', 'Master Data Barang')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Produk</h2>
            <p class="text-sm text-gray-500">Kelola master data barang untuk inventory dan penjualan.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, SKU, atau Barcode" class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Brand</label>
                <select name="brand_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Stok -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status Stok</label>
                <select name="stock_status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>Tersedia</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis (0)</option>
                </select>
            </div>

            <!-- Trashed Items Toggle -->
            <div class="lg:col-span-5 flex flex-wrap items-center justify-between pt-2 border-t border-gray-100 mt-2 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="trashed" value="1" {{ request('trashed') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Tampilkan Data Terhapus</span>
                </label>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset Filter</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Produk</th>
                        <th scope="col" class="px-6 py-4 text-center">SKU / Barcode</th>
                        <th scope="col" class="px-6 py-4">Kategori / Brand</th>
                        <th scope="col" class="px-6 py-4 text-right">Harga</th>
                        <th scope="col" class="px-6 py-4 text-center">Stok</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="bg-white border-b hover:bg-gray-50 {{ $product->trashed() ? 'opacity-70 bg-gray-100' : '' }}">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-400 font-bold text-lg">{{ substr($product->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 line-clamp-2">{{ $product->name }}</p>
                                        @if($product->trashed())
                                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-red-100 text-red-800">
                                                Terhapus
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-gray-700">{{ $product->sku }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $product->barcode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 mb-1">
                                    {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                                </div>
                                <div class="text-xs font-medium text-gray-500 mt-1">
                                    {{ $product->brand ? $product->brand->name : 'Tanpa Brand' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-xs text-gray-500">M: Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
                                <div class="text-sm font-bold text-green-600 mt-0.5">J: Rp {{ number_format($product->sale_price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-800">{{ $product->stock }}</span>
                                <div class="text-[10px] text-gray-500">Min: {{ $product->min_stock }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->stock <= 0)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Habis</span>
                                @elseif($product->stock <= $product->min_stock)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menipis</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aman</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($product->trashed())
                                        <!-- Restore Button -->
                                        <form action="{{ route('products.restore', $product->id) }}" method="POST" onsubmit="return confirm('Kembalikan produk ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('products.show', $product->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');" class="inline-block">
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
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <p class="text-base font-medium text-gray-900">Belum ada data produk</p>
                                    <p class="text-sm mt-1">Gunakan tombol 'Tambah Produk' untuk memasukkan data baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
