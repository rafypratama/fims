<x-app-layout>
    @php
        $user = Auth::user();
        
        // Dynamic stats
        $invoiceTodayCount = \App\Models\Invoice::whereDate('created_at', today())->count();
        $draftInvoiceCount = \App\Models\Invoice::where('status', 'Draft')->count();
        
        // For Surat Jalan Selesai, we can count total DOs completed (e.g. delivery date is past or today)
        $sjSelesaiCount = \App\Models\DeliveryOrder::whereDate('delivery_date', '<=', today())->count();

        // Combined recent documents
        $recentInvoices = \App\Models\Invoice::with('customer')->latest()->take(5)->get()->map(function($inv) {
            return [
                'type' => 'invoice',
                'number' => $inv->invoice_number,
                'customer' => $inv->customer->name,
                'time' => $inv->created_at,
                'status' => $inv->status,
                'url' => route('invoices.show', $inv->id),
                'edit_url' => route('invoices.edit', $inv->id),
            ];
        });

        $recentDOs = \App\Models\DeliveryOrder::with('customer')->latest()->take(5)->get()->map(function($do) {
            return [
                'type' => 'sj',
                'number' => $do->sj_number,
                'customer' => $do->customer->name,
                'time' => $do->created_at,
                'status' => 'Selesai',
                'url' => route('delivery-orders.show', $do->id),
                'edit_url' => route('delivery-orders.edit', $do->id),
            ];
        });

        $recentDocs = $recentInvoices->concat($recentDOs)->sortByDesc('time')->take(5);

        // Draft invoices for "Draft Tertunda"
        $draftInvoices = \App\Models\Invoice::with('customer')->where('status', 'Draft')->latest()->take(3)->get();
    @endphp

    <div class="space-y-6">
        {{-- ===== HEADER ROW ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Selamat Pagi, {{ $user->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">Berikut adalah ringkasan tugas harian Anda hari ini.</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('delivery-orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Surat Jalan Baru
                </a>
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Buat Invoice
                </a>
            </div>
        </div>

        {{-- ===== STATS ROW ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Card 1: Invoice Dibuat Hari Ini --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Invoice Dibuat Hari Ini</span>
                    <span class="text-3xl font-extrabold text-slate-950 block">{{ $invoiceTodayCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>

            {{-- Card 2: Draft Invoice --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Draft Invoice</span>
                    <span class="text-3xl font-extrabold text-slate-950 block">{{ $draftInvoiceCount }}</span>
                    @if($draftInvoiceCount > 0)
                        <a href="{{ route('invoices.index', ['status' => 'Draft']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors inline-flex items-center gap-1">
                            Lanjutkan Draft
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="text-xs text-slate-400">Tidak ada draft aktif</span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>

            {{-- Card 3: Surat Jalan Selesai --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Surat Jalan Selesai</span>
                    <span class="text-3xl font-extrabold text-slate-950 block">{{ $sjSelesaiCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 8h7a1 1 0 011 1v5h-9M13 16h6"/></svg>
                </div>
            </div>
        </div>

        {{-- ===== BOTTOM GRID ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- COLUMN 1: DOKUMEN TERAKHIR DIBUAT (3/5) --}}
            <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Dokumen Terakhir Dibuat</h2>
                    <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-750 transition-colors">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-50 pb-2">
                                <th class="pb-2 pr-3">No. Dokumen</th>
                                <th class="pb-2 pr-3">Customer</th>
                                <th class="pb-2 pr-3">Waktu</th>
                                <th class="pb-2 pr-3 text-center">Status</th>
                                <th class="pb-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @forelse($recentDocs as $doc)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    {{-- Document Number & Icon --}}
                                    <td class="py-3.5 pr-3">
                                        <div class="flex items-center gap-2">
                                            @if($doc['type'] === 'invoice')
                                                @if($doc['status'] === 'Draft')
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 8h7a1 1 0 011 1v5h-9M13 16h6"/></svg>
                                            @endif
                                            <a href="{{ $doc['url'] }}" class="font-semibold text-slate-800 hover:text-indigo-650 transition-colors">{{ $doc['number'] }}</a>
                                        </div>
                                    </td>

                                    {{-- Customer --}}
                                    <td class="py-3.5 pr-3 text-slate-650 max-w-[150px] truncate">
                                        {{ $doc['customer'] }}
                                    </td>

                                    {{-- Time --}}
                                    <td class="py-3.5 pr-3 text-slate-500 text-xs">
                                        {{ $doc['time']->diffForHumans() }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 pr-3 text-center">
                                        @php
                                            $badgeClass = 'bg-slate-50 text-slate-600 border border-slate-100';
                                            if ($doc['status'] === 'Lunas' || $doc['status'] === 'Selesai') {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                                            } elseif ($doc['status'] === 'Draft') {
                                                $badgeClass = 'bg-amber-50 text-amber-700 border border-amber-100';
                                            } elseif ($doc['status'] === 'Dikirim') {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border border-blue-100';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                                            {{ $doc['status'] }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="py-3.5 text-right">
                                        @if($doc['status'] === 'Draft')
                                            <a href="{{ $doc['edit_url'] }}" class="text-xs font-semibold text-indigo-650 hover:underline">Lanjut</a>
                                        @else
                                            <a href="{{ $doc['url'] }}" class="text-slate-400 hover:text-slate-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-450 italic">Belum ada dokumen yang dibuat hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- COLUMN 2: DRAFT TERTUNDDA (2/5) --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-slate-50 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Draft Tertunda</h2>
                </div>

                <div class="space-y-3">
                    @forelse($draftInvoices as $draft)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 text-xs truncate max-w-[150px]">{{ $draft->customer->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $draft->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate">Invoice {{ $draft->invoice_number }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100/60">
                                <span class="text-amber-600 font-semibold">Draft Baru</span>
                                <a href="{{ route('invoices.edit', $draft->id) }}" class="font-semibold text-indigo-650 hover:underline">Lanjut</a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs italic">
                            Tidak ada draft invoice tertunda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
