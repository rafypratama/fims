<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6" x-data="onboardingPage()">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Setup Awal Data</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi awal sistem dengan mengunggah daftar harga dan migrasi arsip invoice lama.</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ===== STATS CARDS ROW ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card: Total SKU --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total SKU Diunggah</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900" id="stat-sku">{{ \App\Models\Product::count() }}</div>
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="inline-flex items-center gap-0.5 text-emerald-600 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Parsed
                    </span>
                </div>
            </div>

            {{-- Card: Invoice Migrasi --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Invoice Migrasi</span>
                    <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900">{{ \App\Models\Invoice::count() }}</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 8.586V6z" clip-rule="evenodd"/></svg>
                    <span>0 Pending Verif</span>
                </div>
            </div>

            {{-- Card: Total Customer --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Customer</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900">{{ \App\Models\Customer::count() }}</div>
                <div class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Data Terkini
                </div>
            </div>

            {{-- Card: Onboarding Progress --}}
            @php
                $totalProducts = \App\Models\Product::count();
                $totalCustomers = \App\Models\Customer::count();
                $totalInvoices = \App\Models\Invoice::count();
                $progressScore = min(100, intval(($totalProducts > 0 ? 30 : 0) + ($totalCustomers > 0 ? 30 : 0) + ($totalInvoices > 0 ? 40 : 0)));
            @endphp
            <div class="bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl shadow-sm p-5 space-y-3 relative overflow-hidden text-white">
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-100">Onboarding Progress</span>
                <div class="flex items-center justify-center">
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"/>
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="white" stroke-width="3" stroke-dasharray="{{ $progressScore }}, 100" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-extrabold">{{ $progressScore }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== UPLOAD PRICE LIST (EXCEL) ===== --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Upload Price List (Excel)</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Unggah daftar harga produk dari brand mitra utama.</p>
                </div>
                <a href="#" class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Template
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
                {{-- Drag & Drop Zone --}}
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('onboarding.import') }}" enctype="multipart/form-data" id="excel-form">
                        @csrf
                        <input type="hidden" name="file_type" value="excel" />
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-indigo-300 hover:bg-indigo-50/30 transition-all cursor-pointer relative group" id="excel-drop-zone">
                            <input type="file" name="file" accept=".xlsx,.xls" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="handleExcelFile(this)" />
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Drag & drop file Excel</p>
                                    <p class="text-xs text-slate-400 mt-1">Maksimal 10MB per file</p>
                                </div>
                                <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                                    Pilih File
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Upload Queue --}}
                <div class="lg:col-span-3 space-y-3" id="excel-queue-container">
                    <div id="excel-file-list">
                        {{-- Uploaded file items will appear here --}}
                        <div class="flex items-center justify-center h-full min-h-[140px] text-slate-300" id="excel-empty-state">
                            <div class="text-center">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs font-medium">Belum ada file diupload</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MIGRASI INVOICE LAMA (PDF) ===== --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Migrasi Invoice Lama (PDF)</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Verifikasi hasil ekstraksi otomatis dari arsip digital.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('onboarding.import') }}" enctype="multipart/form-data" id="pdf-form">
                @csrf
                <input type="hidden" name="file_type" value="pdf" />
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-violet-300 hover:bg-violet-50/30 transition-all cursor-pointer relative group">
                    <input type="file" name="file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="handlePdfFile(this)" />
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 group-hover:bg-violet-100 flex items-center justify-center transition-colors">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-violet-500 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Upload file PDF Invoice Lama</p>
                            <p class="text-xs text-slate-400 mt-1">Sistem akan mengekstrak data secara otomatis</p>
                        </div>
                        <button type="button" class="px-4 py-2 bg-slate-800 text-white font-semibold text-xs rounded-lg hover:bg-slate-900 transition-colors shadow-sm">
                            Pilih File PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== BOTTOM ROW: LOG + HELP ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Log Aktivitas Onboarding --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Log Aktivitas Onboarding</h3>
                <div class="space-y-4 text-sm" id="activity-log">
                    @php
                        $recentProducts = \App\Models\Product::latest()->take(3)->get();
                        $recentInvoices = \App\Models\Invoice::latest()->take(2)->get();
                    @endphp

                    @if($recentProducts->count() > 0)
                        <div class="flex gap-3 items-start">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-slate-700"><strong class="text-slate-900">Admin Utama</strong> berhasil mengunggah <strong>{{ \App\Models\Product::count() }}</strong> data produk ke database utama.</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $recentProducts->first()->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif

                    @if($recentInvoices->count() > 0)
                        <div class="flex gap-3 items-start">
                            <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-slate-700"><strong class="text-slate-900">Sistem AI</strong> mengekstrak data dari {{ $recentInvoices->count() }} invoice PDF.</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $recentInvoices->first()->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif

                    @if(\App\Models\Product::count() == 0 && \App\Models\Invoice::count() == 0)
                        <div class="flex items-center justify-center py-6 text-slate-300">
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-medium">Belum ada aktivitas</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Butuh Bantuan? --}}
            <div class="bg-gradient-to-br from-rose-500 to-orange-500 rounded-xl shadow-sm p-6 space-y-4 text-white">
                <h3 class="text-sm font-bold">Butuh Bantuan?</h3>
                <p class="text-sm text-rose-100 leading-relaxed">Panduan migrasi data untuk admin tersedia di portal dokumentasi terbaru kami.</p>

                <div class="space-y-3 text-xs">
                    <div class="bg-white/15 backdrop-blur rounded-lg p-3 space-y-2">
                        <strong class="block text-white/90">Format Kolom Excel:</strong>
                        <ul class="space-y-1 text-rose-100 list-disc pl-4">
                            <li>Kolom A: Kode Produk (Opsional)</li>
                            <li>Kolom B: Nama Produk (Wajib)</li>
                            <li>Kolom C: Satuan / Unit</li>
                            <li>Kolom D: Harga Jual (Angka)</li>
                        </ul>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-lg p-3">
                        <strong class="block text-white/90 mb-1">Penamaan File:</strong>
                        <p class="text-rose-100">Contoh: <code class="bg-white/20 px-1.5 py-0.5 rounded text-white">Harga PT. Maju Bersama.xlsx</code></p>
                    </div>
                </div>

                <button onclick="window.open('#', '_blank')" class="w-full py-2.5 bg-white/20 hover:bg-white/30 text-white font-semibold text-sm rounded-lg transition-colors backdrop-blur text-center">
                    Buka Dokumentasi
                </button>
            </div>
        </div>
    </div>

    <script>
        function onboardingPage() {
            return {};
        }

        function handleExcelFile(input) {
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(0) + ' KB';
                const ext = fileName.split('.').pop().toLowerCase();

                // Validate file type
                if (!['xlsx', 'xls'].includes(ext)) {
                    alert('Hanya file Excel (.xlsx, .xls) yang diperbolehkan.');
                    input.value = '';
                    return;
                }

                // Hide empty state
                const emptyState = document.getElementById('excel-empty-state');
                if (emptyState) emptyState.style.display = 'none';

                // Add file to queue
                const fileList = document.getElementById('excel-file-list');
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl';
                fileItem.innerHTML = `
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">${fileName}</p>
                        <p class="text-xs text-slate-400">${fileSize}</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 flex-shrink-0">Siap Upload</span>
                `;
                fileList.appendChild(fileItem);

                // Show submit button
                let submitBtn = document.getElementById('excel-submit-btn');
                if (!submitBtn) {
                    submitBtn = document.createElement('button');
                    submitBtn.id = 'excel-submit-btn';
                    submitBtn.type = 'button';
                    submitBtn.className = 'w-full mt-3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm text-center';
                    submitBtn.textContent = 'Proses Impor Katalog';
                    submitBtn.onclick = function() {
                        document.getElementById('excel-form').submit();
                    };
                    document.getElementById('excel-queue-container').appendChild(submitBtn);
                }
            }
        }

        function handlePdfFile(input) {
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const ext = file.name.split('.').pop().toLowerCase();

                if (ext !== 'pdf') {
                    alert('Hanya file PDF (.pdf) yang diperbolehkan.');
                    input.value = '';
                    return;
                }

                // Auto-submit PDF form
                document.getElementById('pdf-form').submit();
            }
        }
    </script>
</x-app-layout>
