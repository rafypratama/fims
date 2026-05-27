<x-app-layout>
    @php
        $bankAccounts = \App\Models\BankAccount::all();
    @endphp

    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
        @csrf

        {{-- ===== HEADER BAR ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl font-bold text-slate-900">Buat Invoice</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">Draft</span>
                    <span class="text-sm font-mono text-slate-400">{{ $invoiceNumber }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <button type="submit" name="status" value="Draft" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    Simpan Draft
                </button>
                <button type="submit" name="status" value="Dikirim" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-semibold text-sm rounded-xl transition-all shadow-md shadow-indigo-200">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terbitkan Invoice
                    </span>
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hidden fields --}}
        <input type="hidden" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" />
        <input type="hidden" id="status-hidden" name="status" value="Draft" />

        {{-- ===== MAIN LAYOUT: 2 COLUMNS ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ========== LEFT COLUMN (3/5) ========== --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- INFORMASI PELANGGAN --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Informasi Pelanggan</h2>
                        <a href="{{ route('customers.create') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">+ Tambah Baru</a>
                    </div>

                    {{-- Customer Select --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <select id="customer_id" name="customer_id" required onchange="loadCustomerProducts()" class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all appearance-none bg-white">
                            <option value="">Cari nama pelanggan atau brand...</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->name }} ({{ $cust->brand_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Fields --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="date" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Invoice</label>
                            <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label for="due_date" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo</label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all" />
                        </div>
                    </div>
                </div>

                {{-- DAFTAR PRODUK / LAYANAN --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Daftar Produk/Layanan</h2>
                        <div id="customer-badge" class="hidden">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span id="customer-badge-name">Terdeteksi</span>
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="items-table">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <th class="pb-3 pr-3 w-2/5">Deskripsi</th>
                                    <th class="pb-3 pr-3 w-16 text-center">Qty</th>
                                    <th class="pb-3 pr-3 w-32 text-right">Harga Unit</th>
                                    <th class="pb-3 w-32 text-right">Total</th>
                                    <th class="pb-3 w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50" id="items-tbody">
                                {{-- Items inserted here by JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Add Item Button --}}
                    <button type="button" onclick="addItemRow()" class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors pt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item Baru
                    </button>
                </div>
            </div>

            {{-- ========== RIGHT COLUMN (2/5) ========== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- RINGKASAN BIAYA --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-900">Ringkasan Biaya</h3>

                    <div class="space-y-3 text-sm">
                        {{-- Subtotal --}}
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Subtotal</span>
                            <span id="label-subtotal" class="font-semibold text-slate-800">Rp 0</span>
                        </div>

                        {{-- PPN --}}
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500">PPN (11%)</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="use_ppn" name="use_ppn" value="1" onchange="calculateTotals()" class="sr-only peer" {{ old('use_ppn') ? 'checked' : '' }} />
                                    <div class="w-8 h-4.5 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-indigo-600" style="width:32px;height:18px;"></div>
                                </label>
                            </div>
                            <span id="label-ppn" class="font-semibold text-slate-800">Rp 0</span>
                        </div>

                        {{-- DP --}}
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Uang Muka (DP)</span>
                            <div class="flex items-center gap-1">
                                <span class="text-slate-400 text-xs">Rp</span>
                                <input type="number" id="dp_amount" name="dp_amount" value="{{ old('dp_amount', 0) }}" min="0" oninput="updateDpByAmount()" class="w-24 py-1 px-2 text-sm border border-slate-200 rounded-lg text-right focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400" />
                            </div>
                        </div>
                        <input type="hidden" id="dp_percent" name="dp_percent" value="{{ old('dp_percent', 0) }}" />

                        {{-- Grand Total --}}
                        <div class="border-t border-slate-100 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Grand Total</span>
                                <span id="label-grand-total" class="text-2xl font-extrabold text-slate-900">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFORMASI PEMBAYARAN --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h3 class="text-sm font-bold text-slate-900">Informasi Pembayaran</h3>
                    </div>

                    @if($bankAccounts->count() > 0)
                        <div class="space-y-3">
                            @foreach($bankAccounts as $bank)
                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $bank->bank_name }}</p>
                                    <p class="text-base font-bold text-slate-800 font-mono mt-1">{{ $bank->account_number }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">a.n. {{ $bank->account_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Belum ada rekening bank. Tambahkan di menu Settings.</p>
                    @endif

                    {{-- Notes --}}
                    <div class="border-t border-slate-100 pt-3">
                        <textarea id="notes" name="notes" rows="3" placeholder="Catatan: Mohon lakukan bukti transfer melalui portal pelanggan atau email ke billing@fims.co.id" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400 resize-none bg-amber-50/40">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- PENGATURAN INVOICE --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Pengaturan Invoice</h3>

                    {{-- Status Selection --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Invoice</label>
                        <select id="status_select" onchange="document.getElementById('status-hidden').value = this.value" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                            @foreach(['Draft', 'PO', 'Dikirim', 'Dibayar Sebagian', 'Lunas', 'Jatuh Tempo', 'Dibatalkan'] as $st)
                                <option value="{{ $st }}" {{ old('status', 'Draft') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 pt-1">
                        {{-- Kirim Email Otomatis --}}
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900 transition-colors">Kirim Email Otomatis</span>
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-colors" />
                        </label>

                        {{-- Gunakan Watermark --}}
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-slate-700 font-medium group-hover:text-slate-900 transition-colors">Gunakan Watermark</span>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" />
                                <div class="w-8 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-indigo-600" style="width:32px;height:18px;"></div>
                            </div>
                        </label>
                    </div>

                    {{-- Preview PDF --}}
                    <div class="border-t border-slate-100 pt-3">
                        <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== JAVASCRIPT ===== --}}
    <script>
        let customerProducts = [];
        let itemIndex = 0;

        function loadCustomerProducts() {
            const customerId = document.getElementById('customer_id').value;
            const tbody = document.getElementById('items-tbody');
            const badge = document.getElementById('customer-badge');
            const badgeName = document.getElementById('customer-badge-name');

            tbody.innerHTML = '';
            itemIndex = 0;
            calculateTotals();

            if (!customerId) {
                customerProducts = [];
                badge.classList.add('hidden');
                return;
            }

            // Show customer badge
            const customerSelect = document.getElementById('customer_id');
            const selectedText = customerSelect.options[customerSelect.selectedIndex].text;
            badgeName.textContent = 'Terdeteksi: ' + selectedText.split('(')[0].trim();
            badge.classList.remove('hidden');

            fetch(`/api/customers/${customerId}/products`)
                .then(res => res.json())
                .then(data => {
                    customerProducts = data;
                    addItemRow();
                })
                .catch(err => {
                    console.error("Gagal memuat produk customer:", err);
                });
        }

        function addItemRow() {
            if (customerProducts.length === 0) {
                alert("Harap pilih customer terlebih dahulu atau pastikan customer memiliki produk di katalog.");
                return;
            }

            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;

            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "group";

            let optionsHtml = '<option value="">-- Pilih Produk --</option>';
            customerProducts.forEach(prod => {
                optionsHtml += `<option value="${prod.id}" data-price="${prod.default_price}" data-unit="${prod.unit}">${prod.name}${prod.code ? ' (' + prod.code + ')' : ''}</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 pr-3">
                    <select name="items[${itemIndex}][product_id]" required onchange="onProductSelect(${itemIndex}, this)" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all bg-white">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 pr-3 text-center">
                    <input type="number" name="items[${itemIndex}][qty]" value="1" min="0.01" step="any" required oninput="onItemUpdate(${itemIndex})" id="qty-input-${itemIndex}" class="w-16 py-2 px-2 text-sm border border-slate-200 rounded-lg text-center focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400" />
                </td>
                <td class="py-3 pr-3 text-right">
                    <div class="flex items-center gap-1 justify-end">
                        <span class="text-xs text-slate-400">Rp</span>
                        <input type="number" name="items[${itemIndex}][unit_price]" value="0" min="0" step="any" required oninput="onItemUpdate(${itemIndex})" id="price-input-${itemIndex}" class="w-28 py-2 px-2 text-sm border border-slate-200 rounded-lg text-right focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400" />
                    </div>
                </td>
                <td class="py-3 text-right">
                    <span id="subtotal-label-${itemIndex}" class="text-sm font-bold text-indigo-600">Rp 0</span>
                </td>
                <td class="py-3 pl-2 text-right">
                    <button type="button" onclick="removeItemRow('${rowId}')" class="w-7 h-7 inline-flex items-center justify-center text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
        }

        function removeItemRow(rowId) {
            const tr = document.getElementById(rowId);
            if (tr) {
                tr.remove();
                calculateTotals();
            }
        }

        function onProductSelect(idx, selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const priceInput = document.getElementById(`price-input-${idx}`);

            if (selectedOpt && selectedOpt.value) {
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
                priceInput.value = price;
            } else {
                priceInput.value = 0;
            }
            onItemUpdate(idx);
        }

        function onItemUpdate(idx) {
            const qty = parseFloat(document.getElementById(`qty-input-${idx}`).value) || 0;
            const price = parseFloat(document.getElementById(`price-input-${idx}`).value) || 0;
            const subtotalLabel = document.getElementById(`subtotal-label-${idx}`);

            const subtotal = qty * price;
            subtotalLabel.textContent = formatRupiah(subtotal);

            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;

            const qtyInputs = document.querySelectorAll('[id^="qty-input-"]');
            qtyInputs.forEach(input => {
                const idx = input.id.replace('qty-input-', '');
                const qty = parseFloat(input.value) || 0;
                const price = parseFloat(document.getElementById(`price-input-${idx}`).value) || 0;
                subtotal += (qty * price);
            });

            const usePpn = document.getElementById('use_ppn').checked;
            const ppn = usePpn ? (subtotal * 0.11) : 0;
            const totalBeforeDp = subtotal + ppn;

            document.getElementById('label-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('label-ppn').textContent = formatRupiah(ppn);

            const dpAmountEl = document.getElementById('dp_amount');
            let dpAmount = parseFloat(dpAmountEl.value) || 0;

            const grandTotal = Math.max(0, totalBeforeDp - dpAmount);
            document.getElementById('label-grand-total').textContent = formatRupiah(grandTotal);
        }

        function updateDpByAmount() {
            document.getElementById('dp_percent').value = 0;
            calculateTotals();
        }

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        }

        // Override form submit buttons to set status
        document.querySelectorAll('button[type="submit"][name="status"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                document.getElementById('status-hidden').value = this.value;
                // Remove the name attribute so it doesn't conflict with the hidden input
                this.removeAttribute('name');
            });
        });
    </script>
</x-app-layout>
