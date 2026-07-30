@extends('layouts.app')

@section('page_title', 'Detail Produk')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Produk: {{ $product->name }}</h2>
            <p class="text-sm text-gray-500">Melihat detail informasi dari barang terpilih.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3">
            
            <!-- Foto Produk / Header (Kiri) -->
            <div class="bg-gray-50 p-8 border-b md:border-b-0 md:border-r border-gray-100 flex flex-col items-center justify-center">
                <div class="w-48 h-48 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-center overflow-hidden mb-6">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-6xl font-bold text-gray-300">{{ substr($product->name, 0, 1) }}</span>
                    @endif
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 text-center">{{ $product->name }}</h3>
                <div class="mt-2 flex gap-2 justify-center">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                    </span>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                        {{ $product->brand ? $product->brand->name : 'Tanpa Brand' }}
                    </span>
                </div>

                <div class="mt-8 flex gap-3 w-full justify-center">
                    @if(!$product->trashed())
                        <a href="{{ route('products.edit', $product->id) }}" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>
                    @else
                        <span class="inline-flex px-4 py-2 bg-red-100 text-red-800 text-sm font-medium rounded-lg">Item Terhapus</span>
                    @endif
                </div>
            </div>

            <!-- Detail Data (Kanan) -->
            <div class="md:col-span-2 p-8">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6 pb-2 border-b border-gray-100">Informasi Detail</h4>
                
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                    <!-- SKU -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SKU (Kode Barang)</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $product->sku }}</dd>
                    </div>

                    <!-- Barcode -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $product->barcode ?? '-' }}</dd>
                    </div>

                    <!-- Harga Modal -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Harga Modal (Beli)</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
                        <dd class="mt-1 text-base font-bold text-green-600">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</dd>
                    </div>

                    <!-- Stok Saat Ini -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stok Saat Ini</dt>
                        <dd class="mt-1 flex items-center gap-3">
                            <span class="text-2xl font-bold text-gray-900">{{ $product->stock }}</span>
                            @if($product->stock <= 0)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Habis</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menipis</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aman</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Batas Stok -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Batas Stok Minimum</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $product->min_stock }}</dd>
                    </div>
                    
                    <!-- Dibuat -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Ditambahkan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>

                    <!-- Diperbarui -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
            
        </div>
    </div>
</div>
@endsection
