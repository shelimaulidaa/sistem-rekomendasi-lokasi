<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Saung Aqiqah') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-base-light text-base-dark h-screen overflow-hidden flex" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 w-64 bg-base-white border-r border-gray-200 flex flex-col h-full shadow-sm z-30 transition-transform duration-300 md:translate-x-0">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain" alt="Logo">
                    <span class="text-xl font-bold text-base-dark tracking-tight">Saung Aqiqah</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                
                @role('manajer|direktur')
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                    Dashboard
                </x-sidebar-link>
                @endrole

                @can('manage users')
                <x-sidebar-link :href="route('manajer.users.index')" :active="request()->routeIs('manajer.users.*')" icon="users">
                    Pengguna
                </x-sidebar-link>
                @endcan

                @can('manage kriteria')
                <x-sidebar-link :href="route('manajer.kriteria.index')" :active="request()->routeIs('manajer.kriteria.*')" icon="adjustments">
                    Kriteria
                </x-sidebar-link>
                @endcan

                @can('manage observasi')
                <x-sidebar-link :href="route('manajer.periode.index')" :active="request()->routeIs('manajer.periode.*')" icon="calendar">
                    Periode
                </x-sidebar-link>
                @endcan

                @can('manage observasi')
                <x-sidebar-link :href="route('manajer.observasi.index')" :active="request()->routeIs('manajer.observasi.*') && !request()->routeIs('manajer.observasi.hasil')" icon="document-text">
                    Observasi Lokasi
                </x-sidebar-link>

                <x-sidebar-link :href="route('manajer.hasil.index')" :active="request()->routeIs('manajer.hasil.*')" icon="clipboard-check">
                    Hasil Observasi
                </x-sidebar-link>
                @endcan

                @can('view rekomendasi')
                <x-sidebar-link :href="route('direktur.observasi.index')" :active="request()->routeIs('direktur.observasi.*')" icon="clipboard">
                    {{ __('Hasil Observasi') }}
                </x-sidebar-link>
                @endcan
            </div>

            <!-- Profile Bottom Section -->
            <div class="p-4 border-t border-gray-100">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full focus:outline-none p-2 rounded-md hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-soft-green text-primary flex items-center justify-center font-bold mr-3 flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-base-medium truncate">{{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-full mb-2 left-0 w-full bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50" x-cloak style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-soft-green hover:text-primary transition-colors">Profil Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">
            
            <!-- Mobile Header (Visible only on small screens) -->
            <header class="bg-base-white border-b border-gray-200 h-16 flex items-center justify-between px-4 md:hidden shadow-sm z-10">
                <div class="flex items-center">
                    <span class="text-lg font-bold text-base-dark">Saung Aqiqah</span>
                </div>
                <!-- Mobile menu toggle button -->
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-primary focus:outline-none p-2 rounded-md hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Header -->
            @if (isset($header))
            <header class="bg-base-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
