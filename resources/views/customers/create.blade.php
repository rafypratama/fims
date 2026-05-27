<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tambah Customer Baru</h1>
                <p class="text-sm text-slate-500">Daftarkan customer baru beserta mapping brand-nya.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Customer Name -->
                    <div class="space-y-1">
                        <label for="name" class="text-sm font-semibold text-slate-700">Nama Perusahaan / Customer <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: PT. Sumber Makmur" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('name') border-rose-500 @enderror" />
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Main Brand Name -->
                    <div class="space-y-1">
                        <label for="brand_name" class="text-sm font-semibold text-slate-700">Brand Utama <span class="text-rose-500">*</span></label>
                        <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" placeholder="Contoh: Indomie / Samsung" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('brand_name') border-rose-500 @enderror" />
                        @error('brand_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Extra Brands mapping -->
                <div class="space-y-1">
                    <label for="brands" class="text-sm font-semibold text-slate-700">Brand Tambahan Lainnya (Opsional)</label>
                    <input type="text" id="brands" name="brands" value="{{ old('brands') }}" placeholder="Pisahkan dengan koma, contoh: Aqua, Vit, Cleo" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    <p class="text-xs text-slate-400">Gunakan tanda koma (,) untuk memisahkan beberapa brand tambahan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- PIC -->
                    <div class="space-y-1">
                        <label for="pic" class="text-sm font-semibold text-slate-700">Nama PIC</label>
                        <input type="text" id="pic" name="pic" value="{{ old('pic') }}" placeholder="Contoh: Budi Santoso" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <label for="phone" class="text-sm font-semibold text-slate-700">No. Telepon / HP</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="email" class="text-sm font-semibold text-slate-700">Email Kontak</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Contoh: pic@sumbermakmur.com" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-rose-500 @enderror" />
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="address" class="text-sm font-semibold text-slate-700">Alamat Perusahaan</label>
                    <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap pengiriman / korespondensi..." class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                </div>

                <!-- Action buttons -->
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                        Simpan Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
