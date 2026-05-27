<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hub Master Data</h1>
            <p class="text-sm text-slate-550 mt-1">Kelola data dasar operasional FIMS, termasuk profil pelanggan, katalog produk terisolasi, dan saluran rekening bank tujuan pembayaran.</p>
        </div>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Customers Count -->
            <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-lg text-indigo-650 text-xl font-bold">
                    👥
                </div>
                <div>
                    <div class="text-xs text-slate-450 font-semibold uppercase tracking-wider">Total Pelanggan</div>
                    <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $customersCount }}</div>
                </div>
            </div>

            <!-- Products Count -->
            <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-violet-50 rounded-lg text-violet-650 text-xl font-bold">
                    📦
                </div>
                <div>
                    <div class="text-xs text-slate-450 font-semibold uppercase tracking-wider">Katalog Produk</div>
                    <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $productsCount }}</div>
                </div>
            </div>

            <!-- Bank Accounts Count -->
            <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-650 text-xl font-bold">
                    💳
                </div>
                <div>
                    <div class="text-xs text-slate-450 font-semibold uppercase tracking-wider">Rekening Bank</div>
                    <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $banksCount }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation Hub Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Customer & Brand -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="inline-flex px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded">Dasar</span>
                    <h3 class="text-base font-bold text-slate-900">Customer & Brand</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kelola data profil pelanggan, brand utama, alamat kantor, PIC operasional, nomor telepon, dan status aktif.
                    </p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('customers.index') }}" class="inline-flex w-full justify-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition-colors text-center shadow-sm">
                        Kelola Customer →
                    </a>
                </div>
            </div>

            <!-- Card 2: Catalog Products -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="inline-flex px-2 py-0.5 bg-violet-50 text-violet-700 text-xs font-bold rounded">Isolasi</span>
                    <h3 class="text-base font-bold text-slate-900">Katalog Produk</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kelola inventori dan harga jual terisolasi per customer. Produk satu customer dijamin tidak akan bocor ke customer lainnya.
                    </p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('products.index') }}" class="inline-flex w-full justify-center py-2 bg-slate-800 hover:bg-slate-950 text-white font-semibold text-xs rounded-lg transition-colors text-center shadow-sm">
                        Kelola Produk →
                    </a>
                </div>
            </div>

            <!-- Card 3: Bank Accounts -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 hover:shadow-md transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="inline-flex px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded">Pembayaran</span>
                    <h3 class="text-base font-bold text-slate-900">Rekening Tujuan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Konfigurasi detail nomor rekening bank (BCA, Mandiri, dll.) yang ditampilkan otomatis di cetakan penagihan PDF.
                    </p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('settings.index') }}" class="inline-flex w-full justify-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition-colors text-center shadow-sm">
                        Kelola Rekening Bank →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
