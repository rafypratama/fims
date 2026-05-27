<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | FIMS</title>
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
            <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-indigo-100">
                🔍
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-6xl font-extrabold text-slate-900 tracking-tight">404</h1>
            <h2 class="text-xl font-bold text-slate-850">Halaman Tidak Ditemukan</h2>
            <p class="text-sm text-slate-550 leading-relaxed max-w-sm mx-auto">
                Tautan yang Anda tuju mungkin sudah dihapus, diubah namanya, atau sedang tidak tersedia untuk sementara waktu.
            </p>
        </div>

        <div class="pt-2">
            <a href="/dashboard" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-750 transition-colors shadow-sm">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
