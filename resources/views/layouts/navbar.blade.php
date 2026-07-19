<header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <!-- Page Title -->
        <div class="hidden md:block">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h1>
        </div>
    </div>

    <!-- Right Top Nav: Profile Dropdown -->
    <div class="flex items-center gap-4">
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="flex items-center gap-2 focus:outline-none hover:bg-gray-50 p-1.5 rounded-lg transition-colors">
                <span class="text-sm font-medium text-gray-700 hidden md:block">{{ Auth::user()->name ?? 'Administrator' }}</span>
                <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 font-bold uppercase">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" x-transition.opacity class="absolute right-0 w-56 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden" x-cloak>
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@ajmstore.com' }}</p>
                    <p class="text-[10px] font-bold text-blue-600 mt-1 uppercase">{{ Auth::user()->role->name ?? 'Admin' }}</p>
                </div>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Profil Saya</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Pengaturan</a>
                <div class="border-t border-gray-100"></div>
                
                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
