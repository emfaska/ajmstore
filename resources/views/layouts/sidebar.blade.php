<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden"
    @click="sidebarOpen = false" x-cloak></div>

<!-- Sidebar Container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800">
        <a href="#" class="text-xl font-bold tracking-wider flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">A</div>
            <span>AJM STORE</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Nav -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        @if (auth()->user()->isOwner() || auth()->user()->isAdmin())
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} rounded-lg transition-colors text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Dashboard
            </a>
        @endif

        <!-- Master Data (Dropdown) -->
        <div x-data="{ open: {{ request()->routeIs('products.*', 'categories.*', 'brands.*', 'suppliers.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Master Data
                </div>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-collapse class="pl-11 pr-3 py-2 space-y-1" x-cloak>
                <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('products.*') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Barang</a>
                <a href="{{ Route::has('categories.index') ? route('categories.index') : '#' }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('categories.*') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Kategori</a>
                <a href="{{ Route::has('brands.index') ? route('brands.index') : '#' }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('brands.*') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Brand</a>
                <a href="{{ route('suppliers.index') }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('suppliers.*') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Supplier</a>
                <a href="{{ route('customers.index') }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('customers.*') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Customer</a>
                <a href="#" class="block py-1.5 text-sm text-slate-400 hover:text-white">Kendaraan</a>
            </div>
        </div>

        <!-- Pembelian -->
        <a href="{{ route('purchases.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            Pembelian
        </a>

        <!-- Penjualan (POS) (Dropdown) -->
        <div x-data="{ open: {{ request()->routeIs('sales.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Penjualan (POS)
                </div>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-collapse class="pl-11 pr-3 py-2 space-y-1" x-cloak>
                <a href="{{ route('sales.create') }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('sales.create') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Halaman
                    Kasir (POS)</a>
                <a href="{{ route('sales.index') }}"
                    class="block py-1.5 text-sm {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Daftar
                    Penjualan</a>
            </div>
        </div>

        <!-- Pengeluaran -->
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
            </svg>
            Pengeluaran
        </a>

        <!-- Pemasukan -->
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            Pemasukan
        </a>

        <!-- Saldo Kas -->
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            Saldo Kas
        </a>

        <!-- Laporan (Dropdown) -->
        @if (auth()->user()->isOwner() || auth()->user()->isAdmin())
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors text-sm font-medium">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Laporan
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-2 space-y-1" x-cloak>
                    <a href="{{ route('reports.cash') }}"
                        class="block py-1.5 text-sm {{ request()->routeIs('reports.cash') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Laporan
                        Kas</a>
                    <a href="{{ route('reports.sales') }}"
                        class="block py-1.5 text-sm {{ request()->routeIs('reports.sales') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Laporan
                        Penjualan</a>
                    <a href="{{ route('reports.purchases') }}"
                        class="block py-1.5 text-sm {{ request()->routeIs('reports.purchases') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Laporan
                        Pembelian</a>
                    <a href="{{ route('reports.profit_loss') }}"
                        class="block py-1.5 text-sm {{ request()->routeIs('reports.profit_loss') ? 'text-white font-semibold' : 'text-slate-400 hover:text-white' }}">Laporan
                        Laba Rugi</a>
                </div>
            </div>
        @endif

        <!-- Pengaturan -->
        @if (auth()->user()->isOwner() || auth()->user()->isAdmin())
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} rounded-lg transition-colors text-sm font-medium mt-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Pengaturan
            </a>
        @endif
    </nav>
</aside>
