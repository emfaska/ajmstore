@extends('layouts.app')

@section('page_title', 'Detail Pelanggan')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pelanggan</h2>
                <p class="text-sm text-gray-500">Informasi lengkap pelanggan dan kendaraan terkait.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div
                class="p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h3>
                        @if ($customer->trashed())
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terhapus</span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('customers.edit', $customer->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg shadow hover:bg-yellow-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Pelanggan
                </a>
            </div>

            <div class="p-8">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Pelanggan</dt>
                        <dd class="mt-2 text-base font-medium text-gray-900">{{ $customer->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                        <dd class="mt-2 text-base font-medium text-gray-900">{{ $customer->phone ?? 'Tidak Ada Data' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-2 text-base font-medium text-gray-900">{{ $customer->email ?? 'Tidak Ada Data' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="mt-2 text-base font-medium text-gray-900">{{ $customer->address ?? 'Tidak Ada Data' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                        <dd class="mt-2 text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{ $customer->notes ?? 'Tidak Ada Catatan.' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Ditambahkan</dt>
                        <dd class="mt-2 text-sm text-gray-900">{{ $customer->created_at->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                        <dd class="mt-2 text-sm text-gray-900">{{ $customer->updated_at->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
