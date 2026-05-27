<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FIMS') }}</title>
        @include('partials.favicons')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Avoid flash of collapsed sidebar layout
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        </script>
        <style>
            .sidebar-collapsed .sidebar-expanded-only {
                display: none !important;
            }
            .sidebar-collapsed .sidebar-collapsed-only {
                display: flex !important;
            }
            .sidebar-collapsed .sidebar-width {
                width: 64px !important;
            }
            .sidebar-collapsed .content-margin {
                margin-left: 64px !important;
            }
            @media (max-width: 767px) {
                .content-margin {
                    margin-left: 0 !important;
                }
            }
        </style>
    </head>
    <body class="h-full font-sans antialiased text-slate-900">
        <!-- Sidebar container for Desktop -->
        <aside id="sidebar" class="fixed top-0 bottom-0 left-0 z-40 flex-col hidden bg-white border-r border-slate-200 md:flex sidebar-transition sidebar-width w-[240px]">
            <!-- Header Brand -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-slate-100 relative">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                    <img src="{{ asset('assets/brand/fims-premium-icon-logo.png') }}" alt="FIMS" class="w-10 h-10 object-contain shrink-0" />
                    <span class="text-xl font-bold tracking-tight text-indigo-900 sidebar-expanded-only sidebar-text">FIMS</span>
                </a>
                
                <!-- Expanded view collapse trigger -->
                <button onclick="toggleSidebar()" class="p-1 rounded-md hover:bg-slate-100 text-slate-500 sidebar-expanded-only transition-colors">
                    <!-- Chevron left icon -->
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <!-- Collapsed view interactive expander overlay -->
                <button onclick="toggleSidebar()" class="hidden absolute inset-0 w-full h-full flex items-center justify-center bg-white/90 hover:bg-indigo-50/95 text-indigo-650 transition-all opacity-0 hover:opacity-100 sidebar-collapsed-only" title="Lebarkan Sidebar">
                    <!-- Chevron right icon -->
                    <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Dashboard</span>
                    <span class="sidebar-tooltip md:hidden lg:inline-block">Dashboard</span>
                </a>

                <!-- Invoice -->
                <a href="/invoices" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('invoices*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Invoice</span>
                    <span class="sidebar-tooltip">Invoice</span>
                </a>

                <!-- Surat Jalan -->
                <a href="/delivery-orders" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('delivery-orders*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M21 16V10a1 1 0 00-1-1h-7m8 7a1 1 0 001-1v-4a1 1 0 00-1-1h-3" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Surat Jalan</span>
                    <span class="sidebar-tooltip">Surat Jalan</span>
                </a>

                <!-- Customer & Brand Mapping -->
                <a href="/customers" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('customers*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Customer & Brand</span>
                    <span class="sidebar-tooltip">Customer & Brand</span>
                </a>

                <!-- Produk -->
                <a href="/products" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('products*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Produk</span>
                    <span class="sidebar-tooltip">Produk</span>
                </a>

                @if(Auth::user() && Auth::user()->isAdmin())
                <!-- Master Data -->
                <a href="/master-data" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('master-data*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Master Data</span>
                    <span class="sidebar-tooltip">Master Data</span>
                </a>

                <!-- Manajemen Pengguna -->
                <a href="{{ route('users.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('users*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0-.001h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Manajemen Pengguna</span>
                    <span class="sidebar-tooltip">Manajemen Pengguna</span>
                </a>

                <!-- Onboarding Data -->
                <a href="/onboarding" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('onboarding*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Onboarding Data</span>
                    <span class="sidebar-tooltip">Onboarding Data</span>
                </a>
                @endif

                <!-- Settings (Admin Only) -->
                @if(Auth::user() && Auth::user()->isAdmin())
                <a href="/settings" class="sidebar-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('settings*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-expanded-only sidebar-text">Pengaturan Sistem</span>
                    <span class="sidebar-tooltip">Pengaturan Sistem</span>
                </a>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center justify-between gap-3 overflow-hidden">
                    <div class="flex items-center gap-3">
                        @php
                            $user = Auth::user();
                            $name = $user ? $user->name : 'Guest';
                            $initials = '';
                            foreach (explode(' ', $name) as $word) {
                                $initials .= strtoupper(substr($word, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                            // Generate background color based on name string
                            $colors = ['bg-indigo-600', 'bg-emerald-600', 'bg-blue-600', 'bg-purple-600', 'bg-violet-600', 'bg-pink-600'];
                            $colorIndex = abs(crc32($name)) % count($colors);
                            $bgClass = $colors[$colorIndex];
                        @endphp
                        <!-- Avatar -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-full text-white font-bold text-sm shrink-0 {{ $bgClass }}">
                            {{ $initials }}
                        </div>
                        <div class="sidebar-expanded-only sidebar-text">
                            <div class="text-sm font-semibold truncate max-w-[120px]">{{ $name }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $user && $user->isAdmin() ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-800' }}">
                                {{ $user && $user->isAdmin() ? 'Admin' : 'Karyawan' }}
                            </span>
                        </div>
                    </div>
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-expanded-only">
                        @csrf
                        <button type="button" onclick="confirmLogout(event)" class="p-1.5 rounded-md hover:bg-red-50 text-slate-500 hover:text-red-600" title="Logout">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Sidebar overlay for Mobile drawer -->
        <div id="sidebar-drawer" class="fixed inset-0 z-40 hidden bg-slate-900/50 md:hidden" onclick="toggleDrawer()">
            <div class="w-[240px] h-full bg-white flex flex-col" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between h-16 px-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/brand/fims-premium-icon-logo.png') }}" alt="FIMS" class="w-10 h-10 object-contain shrink-0" />
                        <span class="text-xl font-bold tracking-tight text-indigo-900">FIMS</span>
                    </div>
                    <button onclick="toggleDrawer()" class="p-1 rounded-md hover:bg-slate-100 text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <!-- Invoices -->
                    <a href="/invoices" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('invoices*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Invoice
                    </a>
                    <!-- Surat Jalan -->
                    <a href="/delivery-orders" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('delivery-orders*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M21 16V10a1 1 0 00-1-1h-7m8 7a1 1 0 001-1v-4a1 1 0 00-1-1h-3" />
                        </svg>
                        Surat Jalan
                    </a>
                    <!-- Customer & Brand Mapping -->
                    <a href="/customers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('customers*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Customer & Brand
                    </a>
                    <!-- Produk -->
                    <a href="/products" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('products*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Produk
                    </a>
                    @if(Auth::user() && Auth::user()->isAdmin())
                    <!-- Master Data -->
                    <a href="/master-data" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('master-data*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4" />
                        </svg>
                        Master Data
                    </a>
                    <!-- Manajemen Pengguna -->
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('users*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0-.001h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Manajemen Pengguna
                    </a>
                    <!-- Onboarding -->
                    <a href="/onboarding" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('onboarding*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Onboarding Data
                    </a>
                    @endif
                    <!-- Settings -->
                    @if(Auth::user() && Auth::user()->isAdmin())
                    <a href="/settings" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('settings*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan Sistem
                    </a>
                    @endif
                </nav>
                <div class="p-4 border-t border-slate-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full text-white font-bold text-sm {{ $bgClass }}">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold truncate">{{ $name }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $user && $user->isAdmin() ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-800' }}">
                                {{ $user && $user->isAdmin() ? 'Admin' : 'Karyawan' }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-slate-200 hover:bg-red-50 hover:border-red-100 hover:text-red-600 rounded-lg text-sm font-medium text-slate-700 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Layout content area -->
        <div id="content-container" class="flex flex-col min-h-screen content-margin ml-[240px] sidebar-transition">
            <!-- Header bar / Global search -->
            <header class="flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200">
                <div class="flex items-center gap-4 flex-1">
                    <!-- Mobile drawer toggle -->
                    <button onclick="toggleDrawer()" class="p-2 -ml-2 rounded-md md:hidden hover:bg-slate-100 text-slate-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
                        </svg>
                    </button>
                    <span class="text-sm font-semibold text-slate-700 truncate">Faacos Integrated Management System</span>
                </div>

                <!-- Right header actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('profile.edit') }}" class="hidden items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 sm:flex" title="Profil pengguna">
                        <span class="text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </a>
                </div>
            </header>

            <!-- Page Main Content Slot -->
            <main class="flex-1 p-6 pb-24 md:pb-6">
                {{ $slot }}
            </main>
        </div>

        <!-- Mobile Bottom Navigation Bar -->
        <nav class="fixed bottom-0 left-0 right-0 z-30 flex items-center justify-around h-16 bg-white border-t border-slate-200 md:hidden px-2 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="text-lg">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="/invoices" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-medium transition-colors {{ request()->is('invoices*') ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="text-lg">📄</span>
                <span>Invoice</span>
            </a>
            <a href="/delivery-orders" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-medium transition-colors {{ request()->is('delivery-orders*') ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="text-lg">🚚</span>
                <span>Surat Jalan</span>
            </a>
            <a href="/customers" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-medium transition-colors {{ request()->is('customers*') ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}">
                <span class="text-lg">👥</span>
                <span>Customer</span>
            </a>
            <button onclick="toggleDrawer()" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-medium text-slate-500 hover:text-slate-900">
                <span class="text-lg">☰</span>
                <span>Menu</span>
            </button>
        </nav>

        <!-- Logout Confirmation Dialog Component -->
        <div id="logout-dialog" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-slate-900/50">
            <div class="w-full max-w-sm p-6 bg-white rounded-xl shadow-lg border border-slate-100 mx-4">
                <h3 class="text-lg font-bold text-slate-900 mb-2">Konfirmasi Keluar</h3>
                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin keluar dari sistem FIMS?</p>
                <div class="flex justify-end gap-3">
                    <button onclick="closeLogoutDialog()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar / Layout Script -->
        <script>
            function toggleSidebar() {
                const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
            }

            function toggleDrawer() {
                const drawer = document.getElementById('sidebar-drawer');
                drawer.classList.toggle('hidden');
            }

            function confirmLogout(event) {
                event.preventDefault();
                document.getElementById('logout-dialog').classList.remove('hidden');
            }

            function closeLogoutDialog() {
                document.getElementById('logout-dialog').classList.add('hidden');
            }

            // Swipe to open/close drawer on mobile
            let touchStartX = 0;
            let touchEndX = 0;

            document.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, false);

            document.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, false);

            function handleSwipe() {
                const drawer = document.getElementById('sidebar-drawer');
                const isMobile = window.innerWidth < 768;
                if (!isMobile) return;

                const swipeDistance = touchEndX - touchStartX;
                // Swipe right to open
                if (swipeDistance > 100 && drawer.classList.contains('hidden') && touchStartX < 50) {
                    drawer.classList.remove('hidden');
                }
                // Swipe left to close
                if (swipeDistance < -100 && !drawer.classList.contains('hidden')) {
                    drawer.classList.add('hidden');
                }
            }
        </script>
    </body>
</html>
