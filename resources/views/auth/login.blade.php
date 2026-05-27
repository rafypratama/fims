<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-950">Selamat datang kembali</h1>
        <p class="mt-2 text-sm font-medium text-slate-500">Silakan masukkan detail Anda untuk masuk.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
        @csrf

        <div>
            <label for="email" class="block text-xs font-bold text-slate-700">Email</label>
            <div class="relative mt-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V7.5" />
                    </svg>
                </span>
                <input id="email" class="block h-12 w-full rounded-xl border-slate-200 bg-white pl-11 pr-4 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email') }}" placeholder="admin@fims.co.id" required autofocus autocomplete="username" />
            </div>
            @error('email')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-slate-700">Kata Sandi</label>
            <div class="relative mt-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0119.5 12.75v5.25a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18v-5.25A2.25 2.25 0 016.75 10.5z" />
                    </svg>
                </span>
                <input id="password" class="block h-12 w-full rounded-xl border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500" x-bind:type="showPassword ? 'text' : 'password'" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password" />
                <button type="button" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-indigo-600 focus:outline-none" x-on:click="showPassword = ! showPassword" aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg x-show="! showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A2.99 2.99 0 0012 15a2.99 2.99 0 002.42-1.23M9.88 5.42A9.7 9.7 0 0112 5.25c6 0 9.75 6.75 9.75 6.75a16.9 16.9 0 01-3.12 3.79M6.22 6.22C3.73 7.91 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.53 0 2.92-.44 4.13-1.09" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center gap-2 text-xs font-medium text-slate-500">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span>Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Masuk
        </button>

        <p class="pt-1 text-center text-xs font-medium text-slate-500">
            Belum punya akun?
            <span class="font-bold text-indigo-600">Hubungi administrator Anda.</span>
        </p>
    </form>
</x-guest-layout>
