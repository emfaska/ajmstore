@extends('layouts.app')

@section('page_title', 'Detail Kategori')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('categories.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Kategori</h2>
            <p class="text-sm text-gray-500">Informasi spesifik kelompok produk.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Info -->
        <div class="p-8 border-b border-gray-100 flex justify-between items-center gap-4 bg-gray-50">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $category->name }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-white border border-gray-200 text-gray-700">/{{ $category->slug }}</span>
                    @if($category->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">Nonaktif</span>
                    @endif
                    
                    @if($category->trashed())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terhapus</span>
                    @endif
                </div>
            </div>

            @if(!$category->trashed())
                <a href="{{ route('categories.edit', $category->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg shadow hover:bg-yellow-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Data
                </a>
            @endif
        </div>

        <!-- Detail Data -->
        <div class="p-8">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Deskripsi Kategori</dt>
                    <dd class="mt-2 text-base text-gray-900 bg-white border border-gray-100 p-4 rounded-lg shadow-sm">
                        {!! nl2br(e($category->description ?? 'Tidak ada deskripsi yang tersedia untuk kategori ini.')) !!}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Ditambahkan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
