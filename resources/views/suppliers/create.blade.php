@extends('layouts.app')

@section('page_title', 'Tambah Supplier')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('suppliers.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Supplier Baru</h2>
            <p class="text-sm text-gray-500">Masukkan data detail pemasok untuk keperluan pembelian dan inventory.</p>
        </div>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama Supplier -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier / Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sumber Makmur" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Nama PIC -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama PIC (Penanggung Jawab)</label>
                <input type="text" name="pic_name" value="{{ old('pic_name') }}" placeholder="Contoh: Bpk. Budi" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('pic_name') border-red-500 @enderror">
                @error('pic_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Telepon -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: kontak@sumbermakmur.com" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Tambahan (Opsional)</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Status Aktif -->
            <div class="md:col-span-2 pt-2 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Supplier Aktif</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-8">Jika di-uncheck, supplier tidak akan muncul di daftar pilihan saat input pembelian baru.</p>
                @error('is_active') <p class="mt-1 text-sm text-red-600 ml-8">{{ $message }}</p> @enderror
            </div>
            
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition-colors">Simpan Supplier</button>
        </div>
    </form>
</div>
@endsection
