<x-app-layout>
    @php
        $totalDO = \App\Models\DeliveryOrder::count();
        $thisMonth = \App\Models\DeliveryOrder::whereMonth('delivery_date', now()->month)->whereYear('delivery_date', now()->year)->count();
        $today = \App\Models\DeliveryOrder::whereDate('delivery_date', today())->count();
    @endphp

    <div class="space-y-6">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daftar Surat Jalan</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan pantau semua dokumen pengiriman.</p>
            </div>
            <a href="{{ route('delivery-orders.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-indigo-200 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Surat Jalan Baru
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== STATS CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Total Pengiriman Bulan Ini --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengiriman Bulan Ini</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-extrabold text-slate-900">{{ number_format($thisMonth) }}</span>
                        @if($thisMonth > 0)
                            <span class="text-xs font-semibold text-emerald-600">aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dalam Perjalanan --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Keseluruhan</p>
                    <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($totalDO) }}</span>
                </div>
            </div>

            {{-- Sampai Tujuan (Hari Ini) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengiriman Hari Ini</p>
                    <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($today) }}</span>
                </div>
            </div>
        </div>

        {{-- ===== TABLE CARD ===== --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Filter Bar --}}
            <div class="p-4 border-b border-slate-100">
                <form method="GET" action="{{ route('delivery-orders.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    {{-- Search --}}
                    <div class="relative flex-1 max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Surat Jalan..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                    </div>

                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-colors">
                        Filter
                    </button>

                    @if($search)
                        <a href="{{ route('delivery-orders.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">Reset</a>
                    @endif

                    {{-- Result count --}}
                    <div class="sm:ml-auto text-xs text-slate-400 whitespace-nowrap">
                        Menampilkan {{ $deliveryOrders->firstItem() ?? 0 }}-{{ $deliveryOrders->lastItem() ?? 0 }} dari {{ $deliveryOrders->total() }} hasil
                    </div>
                </form>
            </div>

            {{-- Data Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-5 w-8">
                                <input type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                            </th>
                            <th class="py-3.5 px-5">No. Surat Jalan</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Customer</th>
                            <th class="py-3.5 px-5">Brand</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($deliveryOrders as $do)
                            <tr class="hover:bg-slate-50/60 transition-colors group">
                                {{-- Checkbox --}}
                                <td class="py-4 px-5">
                                    <input type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                </td>

                                {{-- SJ Number --}}
                                <td class="py-4 px-5">
                                    <a href="{{ route('delivery-orders.show', $do->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ $do->sj_number }}
                                    </a>
                                </td>

                                {{-- Date --}}
                                <td class="py-4 px-5 text-slate-500">
                                    {{ $do->delivery_date->format('d M Y') }}
                                </td>

                                {{-- Customer --}}
                                <td class="py-4 px-5">
                                    <span class="font-semibold text-slate-800">{{ $do->customer->name }}</span>
                                </td>

                                {{-- Brand --}}
                                <td class="py-4 px-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $do->customer->brand_name }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="py-4 px-5 text-center">
                                    @php
                                        // Determine status based on delivery date
                                        $doStatus = 'Dikirim';
                                        $statusBadge = ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'];

                                        if ($do->delivery_date->isToday()) {
                                            $doStatus = 'Hari Ini';
                                            $statusBadge = ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'];
                                        } elseif ($do->delivery_date->isPast()) {
                                            $doStatus = 'Selesai';
                                            $statusBadge = ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'];
                                        } elseif ($do->delivery_date->isFuture()) {
                                            $doStatus = 'Dijadwalkan';
                                            $statusBadge = ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'];
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $doStatus }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- View --}}
                                        <a href="{{ route('delivery-orders.show', $do->id) }}" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('delivery-orders.edit', $do->id) }}" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- Print --}}
                                        <a href="{{ route('delivery-orders.print', $do->id) }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Cetak">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>

                                        {{-- PDF --}}
                                        <a href="{{ route('delivery-orders.pdf', $do->id) }}" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Download PDF">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('delivery-orders.destroy', $do->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat Jalan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 inline-flex items-center justify-center hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg transition-all" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-500">Tidak ada surat jalan ditemukan</p>
                                            <p class="text-xs text-slate-400 mt-1">Coba ubah filter atau buat surat jalan baru.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($deliveryOrders->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-slate-400">
                        Menampilkan {{ $deliveryOrders->firstItem() }}-{{ $deliveryOrders->lastItem() }} dari {{ $deliveryOrders->total() }} hasil
                    </span>
                    <div class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if($deliveryOrders->onFirstPage())
                            <span class="w-8 h-8 inline-flex items-center justify-center text-slate-300 rounded-lg text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $deliveryOrders->previousPageUrl() }}" class="w-8 h-8 inline-flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $deliveryOrders->currentPage();
                            $lastPage = $deliveryOrders->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $deliveryOrders->url(1) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">1</a>
                            @if($startPage > 2)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-slate-400 text-xs">...</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-sm bg-indigo-600 text-white rounded-lg font-bold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $deliveryOrders->url($page) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-slate-400 text-xs">...</span>
                            @endif
                            <a href="{{ $deliveryOrders->url($lastPage) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if($deliveryOrders->hasMorePages())
                            <a href="{{ $deliveryOrders->nextPageUrl() }}" class="w-8 h-8 inline-flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="w-8 h-8 inline-flex items-center justify-center text-slate-300 rounded-lg text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
