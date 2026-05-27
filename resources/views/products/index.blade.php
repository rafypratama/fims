<x-app-layout>
    <div class="space-y-6">
        <!-- Top header action bar -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Katalog Produk</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola katalog produk, satuan, dan harga default per customer.</p>
            </div>
            <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm self-start sm:self-auto">
                + Tambah Produk
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-3">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <!-- Filter bar -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('products.index') }}" class="w-full flex flex-col sm:flex-row gap-3">
                <!-- Search text -->
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode produk..." class="flex-1 max-w-xs py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                
                <!-- Customer dropdown filter -->
                <select name="customer_id" class="max-w-xs py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">-- Semua Customer --</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ $customerId == $cust->id ? 'selected' : '' }}>
                            {{ $cust->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-colors">
                    Filter
                </button>
                @if($search || $customerId)
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Kode Produk</th>
                            <th class="py-3 px-4">Nama Produk</th>
                            <th class="py-3 px-4">Customer Terkait</th>
                            <th class="py-3 px-4 text-center">Satuan</th>
                            <th class="py-3 px-4 text-right">Harga Default</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/55 transition-colors">
                                <td class="py-4 px-4 font-mono text-xs font-semibold text-slate-500">
                                    {{ $product->code ?? '-' }}
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-900">
                                    {{ $product->name }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    {{ $product->customer->name }}
                                </td>
                                <td class="py-4 px-4 text-center text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-800">
                                        {{ $product->unit }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right font-semibold text-slate-900">
                                    Rp {{ number_format($product->default_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 text-xs font-semibold rounded-lg text-slate-700 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-transparent hover:bg-rose-50 text-xs font-semibold rounded-lg text-rose-600 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data produk yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
