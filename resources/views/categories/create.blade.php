@extends('layouts.app')

@section('page_title', 'Tambah Kategori')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('categories.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Kategori Baru</h2>
            <p class="text-sm text-gray-500">Buat klasifikasi untuk mengelompokkan produk Anda.</p>
        </div>
    </div>

    <form action="{{ route('categories.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        @csrf

        <div class="grid grid-cols-1 gap-6">
            
            <!-- Nama Kategori -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Oli Mesin" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Slug (URL / Kode Unik) <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Contoh: oli-mesin (dibuat otomatis jika dikosongkan)" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
                @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Kategori</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Status Aktif -->
            <div class="pt-2 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Kategori Aktif</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-8">Jika di-uncheck, kategori tidak akan muncul di daftar pilihan saat menambah produk baru.</p>
                @error('is_active') <p class="mt-1 text-sm text-red-600 ml-8">{{ $message }}</p> @enderror
            </div>
            
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('categories.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition-colors">Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
