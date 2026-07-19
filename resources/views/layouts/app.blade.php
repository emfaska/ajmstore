<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AJM Store Dashboard')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite (Tailwind + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback CDN for Tailwind & AlpineJS (ensure it works immediately) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Wrapper -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Top Navbar -->
            @include('layouts.navbar')

            <!-- Area Content -->
            <main class="w-full grow p-6">
                <!-- If using Component <x-app-layout> -->
                @if(isset($header))
                    <header class="mb-4">
                        {{ $header }}
                    </header>
                @endif
                {{ $slot ?? '' }}
                
                <!-- If using @extends('layouts.app') -->
                @yield('content')
            </main>

            <!-- Footer -->
            @include('layouts.footer')

        </div>
    </div>

    @stack('scripts')
</body>
</html>
