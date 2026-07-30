@extends('layouts.app')

@section('page_title', 'Master Data Brand')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Brand</h2>
            <p class="text-sm text-gray-500">Kelola merek produk untuk mempermudah identifikasi barang.</p>
        </div>
        <a href="{{ route('brands.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Brand
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('brands.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Search Keyword -->
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari Brand</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama Brand atau Slug" class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <!-- Trashed Items Toggle -->
            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer mr-4">
                    <input type="checkbox" name="trashed" value="1" {{ request('trashed') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Tampilkan Terhapus</span>
                </label>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Cari</button>
                <a href="{{ route('brands.index') }}" class="ml-2 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Brand</th>
                        <th scope="col" class="px-6 py-4">Deskripsi</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr class="bg-white border-b hover:bg-gray-50 {{ $brand->trashed() ? 'opacity-70 bg-gray-100' : '' }}">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <p class="font-semibold text-gray-800">{{ $brand->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">/{{ $brand->slug }}</p>
                                @if($brand->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-red-100 text-red-800">Terhapus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="line-clamp-2 text-gray-600" title="{{ $brand->description }}">{{ $brand->description ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($brand->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($brand->trashed())
                                        <!-- Restore Button -->
                                        <form action="{{ route('brands.restore', $brand->id) }}" method="POST" onsubmit="return confirm('Kembalikan brand ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('brands.show', $brand->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('brands.edit', $brand->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Hapus brand ini?');" class="inline-block">
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
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <p class="text-base font-medium text-gray-900">Belum ada data brand</p>
                                    <p class="text-sm mt-1">Gunakan tombol 'Tambah Brand' untuk membuat merek produk baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($brands->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $brands->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
