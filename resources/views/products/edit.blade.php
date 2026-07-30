@extends('layouts.app')

@section('page_title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Produk: {{ $product->name }}</h2>
            <p class="text-sm text-gray-500">Perbarui informasi barang master data.</p>
        </div>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama Produk -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Brand / Merek</label>
                <select name="brand_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('brand_id') border-red-500 @enderror">
                    <option value="">-- Pilih Brand --</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
                @error('brand_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">SKU (Kode Barang) <span class="text-red-500">*</span></label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 uppercase @error('sku') border-red-500 @enderror">
                @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Barcode (Opsional)</label>
                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('barcode') border-red-500 @enderror">
                @error('barcode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Harga Beli -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Beli (Modal) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" required class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('purchase_price') border-red-500 @enderror">
                </div>
                @error('purchase_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Harga Jual -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" required class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('sale_price') border-red-500 @enderror">
                </div>
                @error('sale_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Stok -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Saat Ini <span class="text-red-500">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 @error('stock') border-red-500 @enderror" readonly title="Stok hanya dapat diubah melalui transaksi">
                <p class="mt-1 text-xs text-blue-600 font-medium">Stok hanya bisa diubah melalui form ini saat inisialisasi awal. (Abaikan jika tidak mengerti)</p>
                <!-- Because it's read only here ideally stock is managed through purchases/sales, but we keep it editable for now if they just want master edit -->
                <script>document.getElementsByName('stock')[0].removeAttribute('readonly'); document.getElementsByName('stock')[0].classList.remove('bg-gray-50');</script>
                @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Min Stok -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Batas Stok Minimum <span class="text-red-500">*</span></label>
                <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('min_stock') border-red-500 @enderror">
                @error('min_stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Foto -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Produk (Opsional)</label>
                
                @if($product->image)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-1">Foto saat ini:</p>
                        <img src="{{ Storage::url($product->image) }}" alt="Foto Produk" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif

                <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg @error('image') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Pilih file baru jika ingin mengganti. Format: JPG, JPEG, PNG. Maks: 2MB.</p>
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg shadow transition-colors">Perbarui Produk</button>
        </div>
    </form>
</div>
@endsection
