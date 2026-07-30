@extends('layouts.app')

@section('page_title', 'Detail Supplier')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('suppliers.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Supplier</h2>
            <p class="text-sm text-gray-500">Informasi lengkap terkait rekanan pemasok.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Info -->
        <div class="p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                    {{ substr($supplier->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $supplier->name }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        @if($supplier->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                        @endif
                        
                        @if($supplier->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terhapus</span>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$supplier->trashed())
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg shadow hover:bg-yellow-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Data
                </a>
            @endif
        </div>

        <!-- Detail Data -->
        <div class="p-8">
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6 pb-2 border-b border-gray-100">Informasi Profil</h4>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Nama Penanggung Jawab (PIC)
                    </dt>
                    <dd class="mt-2 text-base font-medium text-gray-900">{{ $supplier->pic_name ?? 'Tidak Ada Data' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Nomor Telepon
                    </dt>
                    <dd class="mt-2 text-base font-medium text-gray-900">{{ $supplier->phone ?? 'Tidak Ada Data' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Alamat Email
                    </dt>
                    <dd class="mt-2 text-base font-medium text-gray-900">{{ $supplier->email ?? 'Tidak Ada Data' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Alamat Lengkap
                    </dt>
                    <dd class="mt-2 text-base font-medium text-gray-900">{{ $supplier->address ?? 'Tidak Ada Data' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Tambahan
                    </dt>
                    <dd class="mt-2 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! nl2br(e($supplier->description ?? 'Tidak Ada Catatan.')) !!}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Ditambahkan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $supplier->created_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $supplier->updated_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>
            </dl>
        </div>

    </div>
</div>
@endsection
