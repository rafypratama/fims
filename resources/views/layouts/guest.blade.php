<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @include('partials.favicons')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        @if (request()->routeIs('login'))
        <div class="min-h-screen overflow-hidden bg-[#171447] lg:grid lg:grid-cols-[42%_1fr]">
            <aside class="relative hidden min-h-screen flex-col justify-between overflow-hidden px-8 py-8 text-white lg:flex xl:px-12">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_24%_20%,rgba(99,102,241,0.30),transparent_34%),radial-gradient(circle_at_82%_76%,rgba(14,165,233,0.16),transparent_30%)]"></div>
                <div class="absolute inset-y-0 right-0 w-px bg-white/10"></div>

                <a href="/" class="relative inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-[0_18px_45px_rgba(0,0,0,0.24)]">
                    <img src="{{ asset('assets/brand/fims-premium-icon-logo.png') }}" alt="FIMS" class="h-8 w-8 object-contain" />
                </a>

                <div class="relative max-w-sm rounded-2xl border border-white/10 bg-white/10 p-6 shadow-[0_24px_80px_rgba(0,0,0,0.28)] backdrop-blur">
                    <p class="text-3xl font-extrabold leading-tight tracking-tight text-white">
                        Penagihan efisien dimulai di sini.
                    </p>
                    <p class="mt-5 text-sm font-medium leading-6 text-indigo-100/85">
                        Akses sistem manajemen faktur perusahaan Anda. Pemrosesan data yang aman, cepat, dan andal.
                    </p>
                </div>

                <p class="relative text-xs font-medium text-indigo-100/70">
                    &copy; {{ date('Y') }} FIMS Enterprise. Hak cipta dilindungi undang-undang.
                </p>
            </aside>

            <main class="flex min-h-screen flex-col bg-white px-6 py-6 lg:rounded-l-[2rem] lg:px-10">
                <div class="flex items-center justify-between lg:hidden">
                    <a href="/" class="inline-flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white shadow">
                            <img src="{{ asset('assets/brand/fims-premium-icon-logo.png') }}" alt="FIMS" class="h-8 w-8 object-contain" />
                        </span>
                        <span class="text-xl font-bold tracking-tight text-indigo-950">FIMS</span>
                    </a>
                </div>

                <div class="flex flex-1 items-center justify-center py-10">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        @else
        <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div>
                <a href="/">
                    <img src="{{ asset('assets/brand/fims-premium-icon-logo.png') }}" alt="FIMS" class="h-20 w-20 object-contain" />
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        @endif
    </body>
</html>
