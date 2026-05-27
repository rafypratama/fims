<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Detail Produk</h1>
                <p class="text-sm text-slate-500">Perbarui spesifikasi produk dan harga default.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Customer Selection -->
                <div class="space-y-1">
                    <label for="customer_id" class="text-sm font-semibold text-slate-700">Customer Pemilik Katalog <span class="text-rose-500">*</span></label>
                    <select id="customer_id" name="customer_id" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id', $product->customer_id) == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Name -->
                <div class="space-y-1">
                    <label for="name" class="text-sm font-semibold text-slate-700">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Product Code -->
                    <div class="space-y-1">
                        <label for="code" class="text-sm font-semibold text-slate-700">Kode Produk (Opsional)</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>

                    <!-- Unit -->
                    <div class="space-y-1">
                        <label for="unit" class="text-sm font-semibold text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        @error('unit')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Default Price -->
                <div class="space-y-1">
                    <label for="default_price" class="text-sm font-semibold text-slate-700">Harga Jual Default (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-sm">Rp</span>
                        <input type="number" id="default_price" name="default_price" value="{{ old('default_price', $product->default_price) }}" min="0" step="1" required class="w-full py-2 pl-9 pr-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>
                    @error('default_price')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action buttons -->
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
