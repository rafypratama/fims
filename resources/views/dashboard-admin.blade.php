<x-app-layout>
    @php
        $formatCompact = function($amount) {
            $amount = (float) $amount;
            if ($amount >= 1000000000) {
                return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . 'M';
            } elseif ($amount >= 1000000) {
                return 'Rp ' . number_format($amount / 1000000, 0, ',', '.') . 'jt';
            } elseif ($amount >= 1000) {
                return 'Rp ' . number_format($amount / 1000, 0, ',', '.') . 'rb';
            }
            return 'Rp ' . number_format($amount, 0, ',', '.');
        };

        $totalInvoices = $stats['invoice_count'] + ($statusDistribution[0]['count'] ?? 0);
        $totalAll = collect($statusDistribution)->sum('count');
    @endphp

    <div class="space-y-6">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Utama</h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan operasional penagihan hari ini.</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto flex-wrap">
                {{-- Period Selector --}}
                <form method="GET" action="{{ route('dashboard') }}" id="period-form" class="relative">
                    <div class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm shadow-sm relative">
                        <!-- Calendar Button to trigger custom date picker popup -->
                        <button type="button" onclick="toggleCustomDatePicker(event)" class="hover:bg-slate-50 p-1.5 rounded-lg transition-colors text-slate-500 focus:outline-none flex items-center justify-center z-10" title="Pilih Tanggal Kustom">
                            <svg class="w-4 h-4 text-indigo-600 hover:text-indigo-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        
                        <!-- Visible Display Label -->
                        <span class="text-sm font-semibold text-slate-700 mr-1 select-none">{{ $periodLabel }}</span>
                        
                        <!-- Overlay Select for Year and Month only (2 options) -->
                        <select name="period" id="period-select" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            @if($period === 'custom')
                                <option value="custom" selected>Kustom</option>
                            @endif
                        </select>
                        
                        <svg class="w-3.5 h-3.5 text-slate-450 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <!-- Custom Date Picker Dropdown Overlay -->
                    <div id="custom-date-picker-overlay" class="absolute right-0 mt-2 p-4 w-72 bg-white rounded-xl border border-slate-200 shadow-xl z-50 space-y-4 hidden">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih Tanggal Kustom</div>
                        <input type="hidden" id="hidden-period" name="period" value="{{ $period }}" />
                        <div class="space-y-3">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block">Dari Tanggal</label>
                                <input type="date" name="start_date" id="start-date-input" value="{{ $startDate->toDateString() }}" class="w-full py-1.5 px-2.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="end-date-input" value="{{ $endDate->toDateString() }}" class="w-full py-1.5 px-2.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="closeCustomDatePicker()" class="px-3 py-1.5 border border-slate-200 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                            <button type="button" onclick="applyCustomDates()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[11px] font-semibold transition-colors shadow-sm">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Download Report --}}
                <a href="{{ route('dashboard.report', ['period' => $period, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white font-semibold text-sm rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Laporan
                </a>
            </div>
        </div>

        {{-- ===== STAT CARDS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Invoice --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -translate-y-8 translate-x-8 opacity-50"></div>
                <div class="flex items-center justify-between relative">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    @if($stats['invoice_growth'] != 0)
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stats['invoice_growth'] > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $stats['invoice_growth'] > 0 ? '+' : '' }}{{ $stats['invoice_growth'] }}%
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Invoice</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $formatCompact($stats['invoice_amount']) }}</div>
                </div>
            </div>

            {{-- Card 2: Belum Dibayar --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -translate-y-8 translate-x-8 opacity-50"></div>
                <div class="flex items-center justify-between relative">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                        {{ $stats['unpaid_count'] }} Dokumen
                    </span>
                </div>
                <div class="relative">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Belum Dibayar</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $formatCompact($stats['unpaid_amount']) }}</div>
                </div>
            </div>

            {{-- Card 3: Jatuh Tempo --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -translate-y-8 translate-x-8 opacity-50"></div>
                <div class="flex items-center justify-between relative">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    @if($stats['overdue_count'] > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                            {{ $stats['overdue_count'] }} URGENT
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jatuh Tempo</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $formatCompact($stats['overdue_amount']) }}</div>
                </div>
            </div>

            {{-- Card 4: Customer Aktif --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -translate-y-8 translate-x-8 opacity-50"></div>
                <div class="flex items-center justify-between relative">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    @if($stats['new_customers'] > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600">
                            +{{ $stats['new_customers'] }} Baru
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer Aktif</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['active_customers'] }} <span class="text-sm font-semibold text-slate-400">Perusahaan</span></div>
                </div>
            </div>
        </div>

        {{-- ===== MIDDLE ROW: CHART + DISTRIBUTION ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Grafik Tren Pendapatan --}}
            <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Grafik Tren Pendapatan</h2>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-slate-400">
                            <span class="w-2.5 h-2.5 rounded-sm bg-gradient-to-t from-indigo-600 to-violet-500"></span>
                            Pendapatan
                        </span>
                    </div>
                </div>

                {{-- CSS Bar Chart --}}
                <div class="flex items-end gap-1.5 h-48 pt-4 px-1">
                    @foreach($monthlySales as $month)
                        <a href="{{ $month['href'] }}" class="flex-1 flex flex-col items-center gap-1.5 group relative">
                            <div class="w-full rounded-t-md transition-all duration-300 group-hover:opacity-80 cursor-pointer
                                {{ $month['is_current'] ? 'bg-gradient-to-t from-indigo-600 to-violet-500 shadow-sm shadow-indigo-200' : 'bg-gradient-to-t from-indigo-300/70 to-violet-300/50' }}"
                                 style="height: {{ $month['percent'] }}%; min-height: 4px;">
                            </div>
                            <span class="text-[10px] font-semibold {{ $month['is_current'] ? 'text-indigo-600' : 'text-slate-400' }}">{{ $month['label'] }}</span>

                            {{-- Tooltip --}}
                            <div class="absolute bottom-full mb-2 hidden group-hover:block bg-slate-800 text-white text-xs rounded-lg px-3 py-2 whitespace-nowrap z-10 shadow-lg pointer-events-none">
                                <div class="font-bold">{{ $month['label'] }}</div>
                                <div>Rp {{ number_format($month['total'], 0, ',', '.') }}</div>
                                <div class="text-slate-400">{{ $month['count'] }} invoice</div>
                                <div class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45 -mt-1"></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Distribusi Status Invoice --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-50 pb-3">Distribusi Status Invoice</h2>

                <div class="space-y-4">
                    @foreach($statusDistribution as $dist)
                        <a href="{{ $dist['href'] }}" class="block space-y-2 group">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $dist['label'] }}</span>
                                <span class="font-bold {{ $dist['text'] }}">{{ $dist['percent'] }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="{{ $dist['bar'] }} h-full rounded-full transition-all duration-500" style="width: {{ max($dist['percent'], 2) }}%"></div>
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">
                                {{ $dist['count'] }} Invoice
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Total diterbitkan: <span class="font-bold text-slate-700">{{ number_format($stats['invoice_count'], 0, ',', '.') }}</span>
                    </div>
                    <div class="text-xs">
                        Tingkat Penagihan:
                        <span class="font-bold {{ $billingHealth === 'Sehat' ? 'text-emerald-600' : ($billingHealth === 'Perlu Dipantau' ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ $billingHealth }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== BOTTOM ROW: ACTIVITIES + TOP CUSTOMERS ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Aktivitas Terbaru --}}
            <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Aktivitas Terbaru</h2>
                    <a href="{{ route('dashboard.activity-log') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-750 transition-colors">Download Log</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-50">
                                <th class="pb-2.5 pr-3">Waktu</th>
                                <th class="pb-2.5 pr-3">Pengguna</th>
                                <th class="pb-2.5 pr-3">Aksi</th>
                                <th class="pb-2.5 pr-3">Target</th>
                                <th class="pb-2.5 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @forelse($recentActivities as $activity)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 pr-3 text-xs text-slate-500 whitespace-nowrap">
                                        @if($activity['time'])
                                            {{ $activity['time']->format('H:i') }}
                                            <div class="text-[10px] text-slate-400">{{ $activity['time']->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                                {{ $activity['initials'] }}
                                            </div>
                                            <span class="text-xs font-semibold text-slate-700 truncate max-w-[80px]">{{ $activity['user'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-3 text-xs text-slate-600 max-w-[120px]">
                                        {{ $activity['action'] }}
                                        @if(!empty($activity['target_subtitle']))
                                            <div class="text-[10px] text-slate-400 truncate">{{ $activity['target_subtitle'] }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3">
                                        <a href="{{ $activity['href'] }}" class="text-xs font-semibold text-slate-800 hover:text-indigo-600 transition-colors">
                                            {{ $activity['target'] }}
                                        </a>
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $activity['status_class'] }}">
                                            {{ $activity['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 italic text-sm">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top 5 Customer --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-50 pb-3">Top 5 Customer</h2>

                <div class="space-y-3">
                    @php
                        $customerColors = [
                            'bg-violet-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'
                        ];
                    @endphp

                    @forelse($topCustomers as $index => $tc)
                        <a href="{{ $tc['href'] }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg {{ $customerColors[$index % 5] }} flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                    {{ $tc['initials'] }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors truncate max-w-[140px]">
                                        {{ $tc['customer']->name }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $tc['invoices_count'] }} Invoice</div>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-slate-800">Rp {{ number_format($tc['total_revenue'], 0, ',', '.') }}</span>
                        </a>
                    @empty
                        <div class="py-6 text-center text-slate-400 italic text-sm">Belum ada data customer.</div>
                    @endforelse
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-750 transition-colors">
                        Lihat Semua Customer →
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleCustomDatePicker(event) {
            event.stopPropagation();
            const overlay = document.getElementById('custom-date-picker-overlay');
            overlay.classList.toggle('hidden');
        }

        function closeCustomDatePicker() {
            const overlay = document.getElementById('custom-date-picker-overlay');
            overlay.classList.add('hidden');
        }

        function applyCustomDates() {
            const startDate = document.getElementById('start-date-input').value;
            const endDate = document.getElementById('end-date-input').value;
            
            if (!startDate || !endDate) {
                alert('Silakan pilih kedua tanggal.');
                return;
            }
            
            // Set period as custom
            document.getElementById('hidden-period').value = 'custom';
            
            // Submit the form
            document.getElementById('period-form').submit();
        }

        // Close overlay when clicking outside
        document.addEventListener('click', function(event) {
            const overlay = document.getElementById('custom-date-picker-overlay');
            const form = document.getElementById('period-form');
            if (overlay && form && !form.contains(event.target)) {
                overlay.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
