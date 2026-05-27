<x-app-layout>
    <div class="h-[calc(100vh-100px)] flex flex-col md:flex-row gap-4">
        <!-- Left Pane: PDF Viewer -->
        <div class="flex-1 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-1/2 md:h-full">
            <div class="bg-slate-55 p-3 border-b border-slate-200 flex items-center justify-between text-xs font-semibold text-slate-700">
                <span>📄 Preview PDF Invoice Asal</span>
                <a href="{{ $pdfUrl }}" target="_blank" class="text-indigo-600 hover:underline">Buka di Tab Baru ↗</a>
            </div>
            <iframe src="{{ $pdfUrl }}" class="flex-1 w-full h-full border-none"></iframe>
        </div>

        <!-- Right Pane: Invoice Entry Form -->
        <div class="flex-1 bg-white rounded-xl border border-slate-200 shadow-sm overflow-y-auto p-5 flex flex-col h-1/2 md:h-full space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Verifikasi & Entri Data Invoice</h2>
                <p class="text-xs text-slate-500 mt-0.5">Lengkapi formulir di bawah ini berdasarkan dokumen PDF di sebelah kiri.</p>
            </div>

            <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form" class="space-y-4 text-sm">
                @csrf

                <!-- Basic Meta Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="invoice_number" class="text-xs font-bold text-slate-700">Nomor Invoice <span class="text-rose-500">*</span></label>
                        <input type="text" id="invoice_number" name="invoice_number" value="{{ $invoiceNumber }}" required class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg focus:ring-indigo-500" />
                    </div>

                    <div class="space-y-1">
                        <label for="customer_id" class="text-xs font-bold text-slate-700">Customer <span class="text-rose-500">*</span></label>
                        <select id="customer_id" name="customer_id" required onchange="loadCustomerProducts()" class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg focus:ring-indigo-500">
                            <option value="">-- Pilih Customer --</option>
                            @php
                                $nameFromContent = $parsedData['customer_name'] ?? '';
                                $nameFromFile = $parsedData['customer_name_from_file'] ?? '';

                                // Check if either name matches an existing customer
                                $matchedFromContent = null;
                                $matchedFromFile = null;
                                if (!empty($nameFromContent)) {
                                    $matchedFromContent = $customers->first(function($c) use ($nameFromContent) {
                                        return strtolower($c->name) === strtolower($nameFromContent);
                                    });
                                }
                                if (!empty($nameFromFile)) {
                                    $matchedFromFile = $customers->first(function($c) use ($nameFromFile) {
                                        return strtolower($c->name) === strtolower($nameFromFile);
                                    });
                                }

                                // Determine default selection priority: matched existing > content name > file name
                                $defaultSelected = '';
                                if ($matchedFromContent) {
                                    $defaultSelected = 'matched_content';
                                } elseif ($matchedFromFile) {
                                    $defaultSelected = 'matched_file';
                                } elseif (!empty($nameFromContent)) {
                                    $defaultSelected = 'new_content';
                                } elseif (!empty($nameFromFile)) {
                                    $defaultSelected = 'new_file';
                                }
                            @endphp

                            {{-- Detected names from PDF as "Buat Baru" options --}}
                            @if(!empty($nameFromContent) || !empty($nameFromFile))
                                <optgroup label="🔍 Terdeteksi dari PDF">
                                    @if(!empty($nameFromContent) && !$matchedFromContent)
                                        <option value="NEW:{{ $nameFromContent }}" {{ $defaultSelected === 'new_content' ? 'selected' : '' }}>
                                            {{ $nameFromContent }} — dari isi dokumen (Buat Baru)
                                        </option>
                                    @endif
                                    @if(!empty($nameFromFile) && !$matchedFromFile && strtolower($nameFromFile) !== strtolower($nameFromContent))
                                        <option value="NEW:{{ $nameFromFile }}" {{ $defaultSelected === 'new_file' ? 'selected' : '' }}>
                                            {{ $nameFromFile }} — dari nama file (Buat Baru)
                                        </option>
                                    @endif
                                </optgroup>
                            @endif

                            {{-- Existing customers --}}
                            <optgroup label="📋 Customer Terdaftar">
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}"
                                        {{ ($matchedFromContent && $matchedFromContent->id === $cust->id && $defaultSelected === 'matched_content') ? 'selected' : '' }}
                                        {{ ($matchedFromFile && $matchedFromFile->id === $cust->id && $defaultSelected === 'matched_file') ? 'selected' : '' }}
                                    >{{ $cust->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label for="date" class="text-xs font-bold text-slate-700">Tanggal <span class="text-rose-500">*</span></label>
                        <input type="date" id="date" name="date" value="{{ $parsedData['date'] ?? date('Y-m-d') }}" required class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg" />
                    </div>

                    <div class="space-y-1">
                        <label for="due_date" class="text-xs font-bold text-slate-700">Jatuh Tempo</label>
                        <input type="date" id="due_date" name="due_date" class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg" />
                    </div>

                    <div class="space-y-1">
                        <label for="status" class="text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" required class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg">
                            @foreach(['Draft', 'Dikirim', 'Dibayar Sebagian', 'Lunas', 'Jatuh Tempo', 'Dibatalkan'] as $st)
                                <option value="{{ $st }}" {{ $st === 'Lunas' ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Products list rows -->
                <div class="border-t border-slate-100 pt-3 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-xs">Daftar Item / Produk</h3>
                        <button type="button" onclick="addItemRow()" class="px-2 py-1 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded border border-indigo-100 hover:bg-indigo-100 transition-colors">
                            + Tambah Baris
                        </button>
                    </div>

                    <div class="max-h-56 overflow-y-auto border border-slate-150 rounded-lg p-2 bg-slate-50/50">
                        <table class="w-full text-left border-collapse text-xs" id="items-table">
                            <thead>
                                <tr class="border-b border-slate-200 font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="pb-1 w-1/2">Produk</th>
                                    <th class="pb-1 w-12 text-center">Unit</th>
                                    <th class="pb-1 w-16 text-right">Qty</th>
                                    <th class="pb-1 w-24 text-right">Harga</th>
                                    <th class="pb-1 w-6"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="items-tbody">
                                <!-- Products rows go here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes & Calculations -->
                <div class="border-t border-slate-100 pt-3 space-y-3">
                    <div class="space-y-1">
                        <label for="notes" class="text-xs font-bold text-slate-700">Catatan Invoice</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Detail transfer..." class="w-full py-1.5 px-3 text-xs border border-slate-250 rounded-lg"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="use_ppn" name="use_ppn" value="1" onchange="calculateTotals()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        <label for="use_ppn" class="text-xs font-bold text-slate-700">Kenakan PPN (11%)</label>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-3 rounded-lg">
                        <div class="space-y-2">
                            <div>Subtotal: <span id="label-subtotal" class="font-bold text-slate-800">Rp 0</span></div>
                            <div>PPN (11%): <span id="label-ppn" class="font-bold text-slate-800">Rp 0</span></div>
                        </div>
                            <div class="space-y-2 text-right">
                            <div>DP (Jumlah): <input type="number" id="dp_amount" name="dp_amount" value="0" min="0" oninput="updateDpByAmount()" class="w-20 py-0.5 px-1 border border-slate-350 rounded text-right ml-1" /></div>
                            <input type="hidden" name="dp_percent" value="0" />
                            <div class="text-sm font-bold text-slate-900 mt-1">Grand Total: <span id="label-grand-total" class="text-indigo-600">Rp 0</span></div>
                        </div>
                    </div>
                </div>

                <!-- Submit buttons -->
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('onboarding.index') }}" class="px-3 py-1.5 border border-slate-200 text-slate-700 font-semibold rounded-lg text-xs hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white font-semibold rounded-lg text-xs hover:bg-indigo-700 transition-colors shadow-sm">
                        Simpan & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS code for verification page -->
    <script>
        let customerProducts = [];
        let itemIndex = 0;

        function loadCustomerProducts() {
            const customerId = document.getElementById('customer_id').value;
            const tbody = document.getElementById('items-tbody');
            
            if (customerId && customerId.startsWith('NEW:')) {
                customerProducts = [];
                return;
            }

            tbody.innerHTML = '';
            itemIndex = 0;
            calculateTotals();

            if (!customerId) {
                customerProducts = [];
                return;
            }

            fetch(`/api/customers/${customerId}/products`)
                .then(res => res.json())
                .then(data => {
                    customerProducts = data;
                    addItemRow();
                })
                .catch(err => console.error(err));
        }

        function addItemRow(parsedItem = null) {
            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            
            let optionsHtml = '<option value="">-- Pilih --</option>';
            customerProducts.forEach(prod => {
                optionsHtml += `<option value="${prod.id}" data-price="${prod.default_price}" data-unit="${prod.unit}">${prod.name}</option>`;
            });

            if (parsedItem) {
                let matchedProd = customerProducts.find(p => p.name.toLowerCase() === parsedItem.name.toLowerCase());
                if (matchedProd) {
                    optionsHtml = optionsHtml.replace(`value="${matchedProd.id}"`, `value="${matchedProd.id}" selected`);
                } else {
                    optionsHtml += `<option value="NEW:${parsedItem.name}" data-price="${parsedItem.price}" data-unit="${parsedItem.unit}" selected>${parsedItem.name} (Produk Baru)</option>`;
                }
            }

            const qtyVal = parsedItem ? parsedItem.qty : 1;
            const priceVal = parsedItem ? parsedItem.price : 0;
            const unitVal = parsedItem ? parsedItem.unit : 'Pcs';

            tr.innerHTML = `
                <td class="py-1.5 pr-2">
                    <select name="items[${itemIndex}][product_id]" required onchange="onProductSelect(${itemIndex}, this)" class="w-full py-0.5 px-1.5 text-xs border border-slate-250 rounded">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-1.5 pr-2 text-center text-slate-500">
                    <span id="unit-label-${itemIndex}">${unitVal}</span>
                    <input type="hidden" name="items[${itemIndex}][unit]" id="unit-input-${itemIndex}" value="${unitVal}" />
                </td>
                <td class="py-1.5 pr-2">
                    <input type="number" name="items[${itemIndex}][qty]" value="${qtyVal}" min="0.01" step="any" required oninput="onItemUpdate(${itemIndex})" id="qty-input-${itemIndex}" class="w-full py-0.5 px-1.5 text-xs border border-slate-250 rounded text-right" />
                </td>
                <td class="py-1.5 pr-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${priceVal}" min="0" step="any" required oninput="onItemUpdate(${itemIndex})" id="price-input-${itemIndex}" class="w-full py-0.5 px-1.5 text-xs border border-slate-250 rounded text-right" />
                </td>
                <td class="py-1.5 text-right">
                    <button type="button" onclick="removeItemRow('${rowId}')" class="text-rose-600 hover:text-rose-800">
                        ✕
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
            calculateTotals();
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
            const unitInput = document.getElementById(`unit-input-${idx}`);
            
            if (selectedOpt && selectedOpt.value) {
                const price = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
                const unit = selectedOpt.getAttribute('data-unit') || '-';
                
                priceInput.value = price;
                unitLabel.textContent = unit;
                if (unitInput) unitInput.value = unit;
            } else {
                priceInput.value = 0;
                unitLabel.textContent = '-';
                if (unitInput) unitInput.value = 'Pcs';
            }
            onItemUpdate(idx);
        }

        function onItemUpdate(idx) {
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
            calculateTotals();
        }

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        }

        window.addEventListener('DOMContentLoaded', () => {
            const parsedData = @json($parsedData ?? null);
            const customerId = document.getElementById('customer_id').value;

            if (parsedData && parsedData.items && parsedData.items.length > 0) {
                if (customerId && !customerId.startsWith('NEW:')) {
                    fetch(`/api/customers/${customerId}/products`)
                        .then(res => res.json())
                        .then(data => {
                            customerProducts = data;
                            parsedData.items.forEach(item => {
                                addItemRow(item);
                            });
                        })
                        .catch(err => {
                            console.error(err);
                            parsedData.items.forEach(item => {
                                addItemRow(item);
                            });
                        });
                } else {
                    parsedData.items.forEach(item => {
                        addItemRow(item);
                    });
                }
            } else {
                loadCustomerProducts();
            }
        });
    </script>
</x-app-layout>
