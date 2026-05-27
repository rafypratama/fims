<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Detail Customer</h1>
                <p class="text-sm text-slate-500">Perbarui profil customer dan kelola pemetaan brand.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Profile Edit Form -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5 h-fit">
                <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Informasi Customer</h2>
                <form method="POST" action="{{ route('customers.update', $customer->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="space-y-1">
                            <label for="name" class="text-sm font-semibold text-slate-700">Nama Perusahaan <span class="text-rose-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>

                        <!-- Brand Utama -->
                        <div class="space-y-1">
                            <label for="brand_name" class="text-sm font-semibold text-slate-700">Brand Utama <span class="text-rose-500">*</span></label>
                            <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name', $customer->brand_name) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- PIC -->
                        <div class="space-y-1">
                            <label for="pic" class="text-sm font-semibold text-slate-700">Nama PIC</label>
                            <input type="text" id="pic" name="pic" value="{{ old('pic', $customer->pic) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1">
                            <label for="phone" class="text-sm font-semibold text-slate-700">No. Telepon / HP</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="email" class="text-sm font-semibold text-slate-700">Email Kontak</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>

                    <!-- Address -->
                    <div class="space-y-1">
                        <label for="address" class="text-sm font-semibold text-slate-700">Alamat Perusahaan</label>
                        <textarea id="address" name="address" rows="3" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <label for="status" class="text-sm font-semibold text-slate-700">Status Aktif</label>
                        <select id="status" name="status" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="1" {{ old('status', $customer->status) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('status', $customer->status) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Brand List & Management -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6 h-fit">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Brand Pemetaan</h2>
                    <p class="text-xs text-slate-500 mt-1">Daftar brand prinsipal yang terasosiasi dengan customer ini.</p>
                </div>

                <!-- Add brand inline form -->
                <form method="POST" action="{{ route('customers.brands.store', $customer->id) }}" class="space-y-3">
                    @csrf
                    <div class="space-y-1">
                        <label for="new_brand_name" class="text-xs font-semibold text-slate-600">Tambah Brand Baru</label>
                        <div class="flex gap-2">
                            <input type="text" id="new_brand_name" name="brand_name" placeholder="Nama Brand" required class="flex-1 py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm shrink-0">
                                Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Brand list -->
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Brand Saat Ini</span>
                    <ul class="divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden">
                        @forelse($customer->brands as $brand)
                            <li class="flex items-center justify-between p-3 text-sm hover:bg-slate-50 transition-colors">
                                <span class="font-medium text-slate-800">
                                    {{ $brand->name }}
                                    @if(strtolower($brand->name) === strtolower($customer->brand_name))
                                        <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">Utama</span>
                                    @endif
                                </span>
                                <form action="{{ route('customers.brands.destroy', [$customer->id, $brand->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus brand ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="p-3 text-center text-slate-400 text-sm">Belum ada brand terdaftar.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
