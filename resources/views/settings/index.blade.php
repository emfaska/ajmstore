@extends('layouts.app')

@section('title', 'Pengaturan Toko - AJM Store')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Toko</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi toko, tampilan struk, dan konfigurasi default sistem POS.</p>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-medium">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Settings Form -->
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" x-data="settingsForm()">
        @csrf
        @method('PUT')

        <!-- Section: Informasi Toko -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Informasi Toko</h3>
            </div>
            <div class="p-6 space-y-5">
                <!-- Logo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Logo Toko</label>
                    <div class="flex items-start gap-6">
                        <!-- Preview -->
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-full h-full object-cover" alt="Logo Preview">
                                </template>
                                <template x-if="!previewUrl">
                                    @if($settings['shop_logo'])
                                        <img src="{{ Storage::disk('public')->url($settings['shop_logo']) }}" class="w-full h-full object-cover" alt="Logo Toko">
                                    @else
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </template>
                            </div>
                        </div>
                        <!-- Upload Input -->
                        <div class="flex-1">
                            <input type="file" id="shop_logo" name="shop_logo" accept="image/*"
                                @change="handleLogoChange($event)"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="mt-1.5 text-xs text-gray-400">PNG, JPG, WEBP hingga 2MB. Disarankan dimensi 200×200 px.</p>
                            @error('shop_logo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Nama Toko -->
                <div>
                    <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Toko <span class="text-red-500">*</span></label>
                    <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name', $settings['shop_name']) }}"
                        placeholder="Contoh: AJM Store"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm @error('shop_name') border-red-400 @enderror">
                    @error('shop_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div>
                    <label for="shop_address" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Toko</label>
                    <textarea id="shop_address" name="shop_address" rows="3"
                        placeholder="Jl. Contoh No. 123, Kota..."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm resize-none @error('shop_address') border-red-400 @enderror">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                    @error('shop_address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <label for="shop_whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">+62</span>
                        <input type="text" id="shop_whatsapp" name="shop_whatsapp"
                            value="{{ old('shop_whatsapp', $settings['shop_whatsapp']) }}"
                            placeholder="81234567890"
                            class="flex-1 rounded-none rounded-r-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm @error('shop_whatsapp') border-red-400 @enderror">
                    </div>
                    @error('shop_whatsapp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section: Pengaturan Struk -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Pengaturan Struk & Nota</h3>
            </div>
            <div class="p-6">
                <div>
                    <label for="receipt_footer" class="block text-sm font-medium text-gray-700 mb-1.5">Footer / Pesan Struk</label>
                    <textarea id="receipt_footer" name="receipt_footer" rows="3"
                        placeholder="Contoh: Terima kasih telah berbelanja! Barang yang sudah dibeli tidak dapat dikembalikan."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm resize-none @error('receipt_footer') border-red-400 @enderror">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Teks ini akan ditampilkan di bagian bawah setiap nota/struk penjualan.</p>
                    @error('receipt_footer')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section: Default Sistem POS -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Default Sistem POS</h3>
                <p class="text-xs text-gray-400 mt-0.5">Nilai ini digunakan sebagai nilai awal saat membuka halaman kasir baru.</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Pajak Default -->
                <div>
                    <label for="default_tax" class="block text-sm font-medium text-gray-700 mb-1.5">Pajak Default (%)</label>
                    <div class="flex">
                        <input type="number" id="default_tax" name="default_tax" min="0" max="100" step="0.01"
                            value="{{ old('default_tax', $settings['default_tax'] ?? 0) }}"
                            class="flex-1 rounded-none rounded-l-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm @error('default_tax') border-red-400 @enderror">
                        <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Masukkan 0 untuk menonaktifkan pajak default.</p>
                    @error('default_tax')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Diskon Default -->
                <div>
                    <label for="default_discount" class="block text-sm font-medium text-gray-700 mb-1.5">Diskon Default (Rp)</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                        <input type="number" id="default_discount" name="default_discount" min="0" step="100"
                            value="{{ old('default_discount', $settings['default_discount'] ?? 0) }}"
                            class="flex-1 rounded-none rounded-r-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm @error('default_discount') border-red-400 @enderror">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Masukkan 0 untuk menonaktifkan diskon default.</p>
                    @error('default_discount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function settingsForm() {
        return {
            previewUrl: null,
            handleLogoChange(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.previewUrl = null;
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
    }
</script>
@endpush
