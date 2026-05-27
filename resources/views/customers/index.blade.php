<x-app-layout>
    <div class="space-y-6">
        <!-- Top header action bar -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Kelola Customer & Brand</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar customer aktif dan pemetaan brand prinsipal mereka.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm self-start sm:self-auto">
                + Tambah Customer
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-3">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <!-- Filter bar -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('customers.index') }}" class="w-full flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama customer, brand, PIC..." class="flex-1 max-w-md py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-colors">
                    Filter
                </button>
                @if($search)
                    <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors text-center">
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
                            <th class="py-3 px-4">Nama Customer</th>
                            <th class="py-3 px-4">Brand Utama</th>
                            <th class="py-3 px-4">Semua Brand</th>
                            <th class="py-3 px-4">PIC / Kontak</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50/55 transition-colors">
                                <td class="py-4 px-4 font-semibold text-slate-900">
                                    {{ $customer->name }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $customer->brand_name }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-wrap gap-1 max-w-[280px]">
                                        @foreach($customer->brands as $brand)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-slate-100 text-slate-600">
                                                {{ $brand->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs">
                                        <div class="font-medium text-slate-900">{{ $customer->pic ?? '-' }}</div>
                                        <div class="text-slate-500">{{ $customer->phone ?? '' }}</div>
                                        <div class="text-slate-400">{{ $customer->email ?? '' }}</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->status ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $customer->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="inline-flex items-center px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 text-xs font-semibold rounded-lg text-slate-700 transition-colors">
                                        Edit / Kelola Brand
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer ini? Semua data brand, produk, invoice, dan surat jalan terkait akan terhapus.');">
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
                                <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data customer yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
