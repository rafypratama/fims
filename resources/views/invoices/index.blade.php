<x-app-layout>
    <div class="space-y-6">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daftar Invoice</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh tagihan pelanggan.</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('invoices.index', ['export' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </a>
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Buat Invoice Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== TABLE CARD ===== --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Filter Bar --}}
            <div class="p-4 border-b border-slate-100">
                <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    {{-- Search --}}
                    <div class="relative flex-1 max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Invoice, Customer..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                    </div>

                    {{-- Status --}}
                    <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all text-slate-600 bg-white min-w-[140px]">
                        <option value="">Semua Status</option>
                        @foreach(['Draft', 'PO', 'Dikirim', 'Dibayar Sebagian', 'Lunas', 'Jatuh Tempo', 'Dibatalkan'] as $st)
                            <option value="{{ $st }}" {{ $status == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>

                    {{-- Date Range --}}
                    <div class="flex items-center gap-2">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all text-slate-600" />
                        <span class="text-slate-300 text-xs">—</span>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all text-slate-600" />
                    </div>

                    @if(($statusGroup ?? null) === 'unpaid')
                        <input type="hidden" name="status_group" value="unpaid">
                    @endif

                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-colors">
                        Filter
                    </button>

                    @if($search || $status || ($statusGroup ?? null) || $dateFrom || $dateTo)
                        <a href="{{ route('invoices.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">Reset</a>
                    @endif

                    {{-- Result count --}}
                    <div class="sm:ml-auto text-xs text-slate-400 whitespace-nowrap">
                        Menampilkan {{ $invoices->firstItem() ?? 0 }}-{{ $invoices->lastItem() ?? 0 }} dari {{ $invoices->total() }} Invoice
                    </div>
                </form>
            </div>

            {{-- Data Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-5">No. Invoice</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Customer</th>
                            <th class="py-3.5 px-5 text-right">Total</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-slate-50/60 transition-colors group">
                                {{-- Invoice Number --}}
                                <td class="py-4 px-5">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>

                                {{-- Date --}}
                                <td class="py-4 px-5 text-slate-500">
                                    {{ $inv->date->format('d M Y') }}
                                </td>

                                {{-- Customer --}}
                                <td class="py-4 px-5">
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $inv->customer->name }}</span>
                                        @if($inv->customer->address && $inv->customer->address !== 'Dibuat otomatis via verifikasi PDF' && $inv->customer->address !== 'Diimpor otomatis via onboarding')
                                            <span class="block text-xs text-slate-400 mt-0.5">{{ Str::limit($inv->customer->address, 30) }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Grand Total --}}
                                <td class="py-4 px-5 text-right">
                                    <span class="font-semibold text-slate-800">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-4 px-5 text-center">
                                    @php
                                        $badgeConfig = [
                                            'Draft'            => ['bg' => 'bg-slate-100',   'text' => 'text-slate-600',   'dot' => 'bg-slate-400'],
                                            'PO'               => ['bg' => 'bg-purple-50',   'text' => 'text-purple-700',  'dot' => 'bg-purple-500'],
                                            'Dikirim'          => ['bg' => 'bg-blue-50',     'text' => 'text-blue-700',    'dot' => 'bg-blue-500'],
                                            'Dibayar Sebagian' => ['bg' => 'bg-amber-50',    'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
                                            'Lunas'            => ['bg' => 'bg-emerald-50',  'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                                            'Jatuh Tempo'      => ['bg' => 'bg-rose-50',     'text' => 'text-rose-700',    'dot' => 'bg-rose-500'],
                                            'Dibatalkan'       => ['bg' => 'bg-gray-100',    'text' => 'text-gray-500',    'dot' => 'bg-gray-400'],
                                        ];
                                        $badge = $badgeConfig[$inv->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $inv->status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Edit --}}
                                        <a href="{{ route('invoices.edit', $inv->id) }}" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- PDF --}}
                                        <a href="{{ route('invoices.pdf', $inv->id) }}" class="w-8 h-8 inline-flex items-center justify-center border border-slate-200 hover:bg-slate-50 text-slate-500 hover:text-slate-700 rounded-lg transition-all" title="Download PDF">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>

                                        {{-- Push to SJ / SJ status --}}
                                        @if($inv->deliveryOrders->count() > 0)
                                            @php $do = $inv->deliveryOrders->first(); @endphp
                                            <a href="{{ route('delivery-orders.show', $do->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-lg border border-emerald-100 transition-all" title="Lihat Surat Jalan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                SJ
                                            </a>
                                        @else
                                            <a href="{{ route('delivery-orders.create', ['invoice_id' => $inv->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg border border-indigo-100 transition-all" title="Buat Surat Jalan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                Push ke SJ
                                            </a>
                                        @endif

                                        {{-- Delete --}}
                                        <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?');">
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
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-500">Tidak ada invoice ditemukan</p>
                                            <p class="text-xs text-slate-400 mt-1">Coba ubah filter atau buat invoice baru.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($invoices->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-slate-400">
                        Halaman {{ $invoices->currentPage() }} dari {{ $invoices->lastPage() }}
                    </span>
                    <div class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if($invoices->onFirstPage())
                            <span class="w-8 h-8 inline-flex items-center justify-center text-slate-300 rounded-lg text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" class="w-8 h-8 inline-flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $invoices->currentPage();
                            $lastPage = $invoices->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $invoices->url(1) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">1</a>
                            @if($startPage > 2)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-slate-400 text-xs">...</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-sm bg-indigo-600 text-white rounded-lg font-bold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $invoices->url($page) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="w-8 h-8 inline-flex items-center justify-center text-slate-400 text-xs">...</span>
                            @endif
                            <a href="{{ $invoices->url($lastPage) }}" class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" class="w-8 h-8 inline-flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg transition-colors text-sm">
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
