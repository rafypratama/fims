<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.show', $invoice->id) }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Invoice: {{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-slate-500">Perbarui informasi tagihan dan daftar item produk.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" id="invoice-form" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Form Fields Grid -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Invoice Number -->
                <div class="space-y-1">
                    <label for="invoice_number" class="text-sm font-semibold text-slate-700">Nomor Invoice <span class="text-rose-500">*</span></label>
                    <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>

                <!-- Customer Selection (Disabled to keep catalogue isolated during edit) -->
                <div class="space-y-1">
                    <label for="customer_id" class="text-sm font-semibold text-slate-700">Customer <span class="text-rose-500">*</span></label>
                    <select id="customer_id" name="customer_id" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50 pointer-events-none">
                        <option value="{{ $invoice->customer_id }}">{{ $invoice->customer->name }}</option>
                    </select>
                </div>

                <!-- Invoice Status -->
                <div class="space-y-1">
                    <label for="status" class="text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @foreach(['Draft', 'Dikirim', 'Dibayar Sebagian', 'Lunas', 'Jatuh Tempo', 'Dibatalkan'] as $st)
                            <option value="{{ $st }}" {{ old('status', $invoice->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div class="space-y-1">
                    <label for="date" class="text-sm font-semibold text-slate-700">Tanggal Invoice <span class="text-rose-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $invoice->date->format('Y-m-d')) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>

                <!-- Due Date -->
                <div class="space-y-1">
                    <label for="due_date" class="text-sm font-semibold text-slate-700">Tanggal Jatuh Tempo</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
            </div>

            <!-- Items Section -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Item / Produk</h2>
                    <button type="button" onclick="addItemRow()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-lg transition-colors border border-indigo-100">
                        + Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="items-table">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <th class="pb-2 pr-4 w-1/3">Produk</th>
                                <th class="pb-2 pr-4 w-16 text-center">Satuan</th>
                                <th class="pb-2 pr-4 w-24">Qty</th>
                                <th class="pb-2 pr-4 w-40">Harga Satuan (Rp)</th>
                                <th class="pb-2 pr-4 w-40">Subtotal (Rp)</th>
                                <th class="pb-2 text-right w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="items-tbody">
                            <!-- Existing items loaded via JS on page load -->
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="border-t border-slate-150 pt-4 flex flex-col md:flex-row md:justify-between gap-6">
                    <!-- Notes & Toggle Options -->
                    <div class="flex-1 space-y-4">
                        <div class="space-y-1">
                            <label for="notes" class="text-sm font-semibold text-slate-700">Catatan Invoice</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="use_ppn" name="use_ppn" value="1" onchange="calculateTotals()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ old('use_ppn', $invoice->use_ppn) ? 'checked' : '' }} />
                            <label for="use_ppn" class="text-sm font-semibold text-slate-750">Kenakan PPN (11%)</label>
                        </div>
                    </div>

                    <!-- Calculation Totals -->
                    <div class="w-full md:w-80 space-y-3 text-sm">
                        <!-- Subtotal -->
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span id="label-subtotal">Rp 0</span>
                        </div>
                        <!-- PPN -->
                        <div class="flex justify-between text-slate-600">
                            <span>PPN (11%):</span>
                            <span id="label-ppn">Rp 0</span>
                        </div>

                        <!-- Down Payment inputs -->
                        <div class="border-t border-slate-100 pt-3 space-y-2">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Uang Muka (DP)</div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-xs text-slate-500 font-medium">Persentase (%)</label>
                                    <input type="number" id="dp_percent" name="dp_percent" value="{{ old('dp_percent', $invoice->dp_percent) }}" min="0" max="100" oninput="updateDpByPercent()" class="w-full py-1 px-2 text-xs border border-slate-250 rounded focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs text-slate-500 font-medium">Jumlah (Rp)</label>
                                    <input type="number" id="dp_amount" name="dp_amount" value="{{ old('dp_amount', $invoice->dp_amount) }}" min="0" oninput="updateDpByAmount()" class="w-full py-1 px-2 text-xs border border-slate-250 rounded focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="border-t border-slate-200 pt-3 flex justify-between font-bold text-lg text-slate-900">
                            <span>Grand Total:</span>
                            <span id="label-grand-total" class="text-indigo-600">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Smart autofill JS script -->
    <script>
        let customerProducts = @json($products);
        let itemIndex = 0;

        // Initialize with existing invoice items
        const existingItems = @json($invoice->items);
        
        window.addEventListener('DOMContentLoaded', () => {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItemRowWithData(item);
                });
            } else {
                addItemRow();
            }
            calculateTotals();
        });

        function addItemRowWithData(item) {
            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "hover:bg-slate-50/50 transition-colors";
            
            let optionsHtml = '<option value="">-- Pilih Produk --</option>';
            customerProducts.forEach(prod => {
                const selected = prod.id == item.product_id ? 'selected' : '';
                optionsHtml += `<option value="${prod.id}" data-price="${prod.default_price}" data-unit="${prod.unit}" ${selected}>${prod.name} (${prod.code || '-'})</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 pr-4">
                    <select name="items[${itemIndex}][product_id]" required onchange="onProductSelect(${itemIndex}, this)" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 pr-4 text-center">
                    <span id="unit-label-${itemIndex}" class="text-sm font-semibold text-slate-500">${item.unit}</span>
                </td>
                <td class="py-3 pr-4">
                    <input type="number" name="items[${itemIndex}][qty]" value="${item.qty}" min="0.01" step="any" required oninput="onItemUpdate(${itemIndex})" id="qty-input-${itemIndex}" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="py-3 pr-4">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${item.unit_price}" min="0" step="any" required oninput="onItemUpdate(${itemIndex})" id="price-input-${itemIndex}" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="py-3 pr-4 text-right">
                    <span id="subtotal-label-${itemIndex}" class="text-sm font-bold text-slate-800">${formatRupiah(item.subtotal)}</span>
                </td>
                <td class="py-3 text-right">
                    <button type="button" onclick="removeItemRow('${rowId}')" class="p-1 text-rose-600 hover:bg-rose-50 rounded-md">
                        🗑️
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
        }

        function addItemRow() {
            if (customerProducts.length === 0) {
                alert("Katalog produk customer kosong.");
                return;
            }

            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "hover:bg-slate-50/50 transition-colors";
            
            let optionsHtml = '<option value="">-- Pilih Produk --</option>';
            customerProducts.forEach(prod => {
                optionsHtml += `<option value="${prod.id}" data-price="${prod.default_price}" data-unit="${prod.unit}">${prod.name} (${prod.code || '-'})</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 pr-4">
                    <select name="items[${itemIndex}][product_id]" required onchange="onProductSelect(${itemIndex}, this)" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 pr-4 text-center">
                    <span id="unit-label-${itemIndex}" class="text-sm font-semibold text-slate-500">-</span>
                </td>
                <td class="py-3 pr-4">
                    <input type="number" name="items[${itemIndex}][qty]" value="1" min="0.01" step="any" required oninput="onItemUpdate(${itemIndex})" id="qty-input-${itemIndex}" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="py-3 pr-4">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="0" min="0" step="any" required oninput="onItemUpdate(${itemIndex})" id="price-input-${itemIndex}" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="py-3 pr-4 text-right">
                    <span id="subtotal-label-${itemIndex}" class="text-sm font-bold text-slate-800">Rp 0</span>
                </td>
                <td class="py-3 text-right">
                    <button type="button" onclick="removeItemRow('${rowId}')" class="p-1 text-rose-600 hover:bg-rose-50 rounded-md">
                        🗑️
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
            const unitLabel = document.getElementById(`unit-label-${idx}`);
            
            if (selectedOpt && selectedOpt.value) {
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
                const unit = selectedOpt.getAttribute('data-unit') || '-';
                
                priceInput.value = price;
                unitLabel.textContent = unit;
            } else {
                priceInput.value = 0;
                unitLabel.textContent = '-';
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

            const dpPercentEl = document.getElementById('dp_percent');
            const dpAmountEl = document.getElementById('dp_amount');
            let dpAmount = parseFloat(dpAmountEl.value) || 0;
            const dpPercent = parseFloat(dpPercentEl.value) || 0;

            if (dpPercent > 0) {
                dpAmount = (totalBeforeDp * dpPercent) / 100;
                dpAmountEl.value = Math.round(dpAmount);
            }

            const grandTotal = Math.max(0, totalBeforeDp - dpAmount);
            document.getElementById('label-grand-total').textContent = formatRupiah(grandTotal);
        }

        function updateDpByPercent() {
            calculateTotals();
        }

        function updateDpByAmount() {
            document.getElementById('dp_percent').value = 0;
            calculateTotals();
        }

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        }
    </script>
</x-app-layout>
