<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | FIMS</title>
    @include('partials.favicons')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full text-center space-y-6">
        <!-- Brand Logo Hexagon -->
        <div class="flex justify-center">
            <div class="h-16 w-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-rose-100 animate-pulse">
                🛑
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-6xl font-extrabold text-slate-900 tracking-tight">403</h1>
            <h2 class="text-xl font-bold text-slate-850">Akses Ditolak / Terbatas</h2>
            <p class="text-sm text-slate-550 leading-relaxed max-w-sm mx-auto">
                Maaf, akun Anda tidak memiliki izin atau wewenang untuk mengakses halaman ini. Silakan hubungi Administrator FIMS.
            </p>
        </div>

        <div class="pt-2">
            <a href="/dashboard" class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white font-semibold text-sm rounded-xl hover:bg-slate-850 transition-colors shadow-sm">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
