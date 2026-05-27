<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- ===== BREADCRUMB & HEADER ===== --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('delivery-orders.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <div class="text-xs text-slate-400 font-semibold mb-0.5 flex items-center gap-1.5">
                    <a href="{{ route('delivery-orders.index') }}" class="hover:text-slate-650 transition-colors">Surat Jalan</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-500">Buat Baru</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Buat Surat Jalan Baru</h1>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('delivery-orders.store') }}" id="do-form" class="space-y-6">
            @csrf

            {{-- ===== CARD 1: INFORMASI DOKUMEN ===== --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h2 class="text-base font-bold text-slate-800">Informasi Dokumen</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- No. Surat Jalan --}}
                    <div class="space-y-1.5">
                        <label for="sj_number" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Surat Jalan</label>
                        <input type="text" id="sj_number" name="sj_number" value="{{ old('sj_number', $sjNumber) }}" readonly required class="w-full py-2.5 px-3 text-sm border border-slate-200 bg-slate-50 text-slate-500 font-semibold rounded-lg cursor-not-allowed pointer-events-none" />
                    </div>

                    {{-- Tanggal Pengiriman --}}
                    <div class="space-y-1.5">
                        <label for="delivery_date" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Pengiriman</label>
                        <input type="date" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all text-slate-700 bg-white" />
                    </div>
                </div>

                {{-- No. Referensi Invoice --}}
                <div class="space-y-1.5">
                    <label for="invoice_id" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Referensi Invoice (Opsional)</label>
                    @if($invoice)
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}" />
                        <input type="text" id="invoice_link_label" value="{{ $invoice->invoice_number }}" readonly class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-500 font-semibold cursor-not-allowed pointer-events-none" />
                    @else
                        <select name="invoice_id" id="invoice_id" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all appearance-none bg-white text-slate-700">
                            <option value="">Pilih Invoice untuk menarik data...</option>
                        </select>
                    @endif
                </div>
            </div>

            {{-- ===== CARD 2: DETAIL PENGIRIMAN ===== --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    <h2 class="text-base font-bold text-slate-800">Detail Pengiriman</h2>
                </div>

                {{-- Pilih Customer / Brand --}}
                <div class="space-y-1.5">
                    <label for="customer_id" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pilih Customer / Brand</label>
                    @if($invoice)
                        <select id="customer_id" name="customer_id" required class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed pointer-events-none">
                            <option value="{{ $invoice->customer_id }}" data-name="{{ $invoice->customer->name }}" data-pic="{{ $invoice->customer->pic }}" data-address="{{ $invoice->customer->address }}">{{ $invoice->customer->name }} ({{ $invoice->customer->brand_name }})</option>
                        </select>
                    @else
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <select id="customer_id" name="customer_id" required onchange="loadCustomerProducts()" class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all appearance-none bg-white text-slate-700">
                                <option value="">Ketik nama customer atau brand...</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" data-name="{{ $cust->name }}" data-pic="{{ $cust->pic }}" data-address="{{ $cust->address }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->brand_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Alamat Pengiriman --}}
                <div class="space-y-1.5">
                    <label for="shipping_address" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat Pengiriman</label>
                    <textarea id="shipping_address" readonly placeholder="Alamat lengkap tujuan..." class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-550 focus:outline-none resize-none min-h-[80px] cursor-not-allowed">{{ $invoice ? $invoice->customer->address : '' }}</textarea>
                </div>

                {{-- Driver & Vehicle License Plate --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama Driver / Kurir --}}
                    <div class="space-y-1.5">
                        <label for="sender_name" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Driver / Kurir</label>
                        <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" placeholder="Cth: Budi Santoso" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                    </div>

                    {{-- Nomor Polisi Kendaraan --}}
                    <div class="space-y-1.5">
                        <label for="receiver_name" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nomor Polisi Kendaraan</label>
                        @php
                            $defaultReceiver = '';
                            if ($invoice) {
                                $pic = trim($invoice->customer->pic);
                                if ($pic && $pic !== '-') {
                                    $defaultReceiver = $pic;
                                } else {
                                    $defaultReceiver = 'PIC ' . $invoice->customer->name;
                                }
                            }
                        @endphp
                        {{-- We still send receiver_name, but the UI labels it "Nomor Polisi Kendaraan" or we keep its name receiver_name to be compatible with DB --}}
                        <input type="text" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', $defaultReceiver) }}" placeholder="Cth: B 1234 CD" class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                    </div>
                </div>
            </div>

            {{-- ===== CARD 3: DAFTAR BARANG ===== --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <h2 class="text-base font-bold text-slate-800">Daftar Barang</h2>
                    </div>
                    @if(!$invoice)
                        <button type="button" onclick="addItemRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg transition-colors border border-indigo-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Baris
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="items-table">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="pb-3 pr-3 w-12 text-center">#</th>
                                <th class="pb-3 pr-3 w-2/5">Nama Item / Produk</th>
                                <th class="pb-3 pr-3 w-24 text-center">Qty</th>
                                <th class="pb-3 pr-3 w-24 text-center">Satuan</th>
                                <th class="pb-3 pr-3">Keterangan</th>
                                @if(!$invoice)
                                    <th class="pb-3 w-8"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50" id="items-tbody">
                            {{-- Items inserted here by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Catatan Pengiriman --}}
                <div class="border-t border-slate-100 pt-4 space-y-1.5">
                    <label for="notes" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Catatan Pengiriman</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Informasi detail jalan, rute, instruksi serah terima..." class="w-full py-2.5 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- ===== SUBMIT BUTTONS ROW ===== --}}
            <div class="flex justify-end items-center gap-3">
                <a href="{{ route('delivery-orders.index') }}" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="is_draft" value="1" class="px-5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    Simpan Draft
                </button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-semibold text-sm rounded-xl transition-all shadow-md shadow-indigo-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- ===== SMART AUTOFILL JAVASCRIPT ===== --}}
    <script>
        let customerProducts = [];
        let itemIndex = 0;

        const linkedInvoice = @json($invoice);

        window.addEventListener('DOMContentLoaded', () => {
            if (linkedInvoice) {
                customerProducts = linkedInvoice.items.map(i => ({
                    id: i.product_id,
                    name: i.product_name,
                    unit: i.unit,
                    default_price: i.unit_price
                }));

                linkedInvoice.items.forEach(item => {
                    addItemRowWithData({
                        product_id: item.product_id,
                        product_name: item.product_name,
                        unit: item.unit,
                        qty: item.qty,
                        notes: ''
                    });
                });
            } else {
                const customerId = document.getElementById('customer_id').value;
                if (customerId) {
                    loadCustomerProducts();
                }
            }
        });

        function loadCustomerProducts() {
            const customerSelect = document.getElementById('customer_id');
            const customerId = customerSelect.value;
            const tbody = document.getElementById('items-tbody');
            tbody.innerHTML = '';
            itemIndex = 0;

            if (!customerId) {
                customerProducts = [];
                updateInvoiceList([]);
                document.getElementById('shipping_address').value = '';
                return;
            }

            const selectedOpt = customerSelect.options[customerSelect.selectedIndex];
            if (selectedOpt) {
                const address = selectedOpt.getAttribute('data-address') ? selectedOpt.getAttribute('data-address').trim() : '';
                document.getElementById('shipping_address').value = address || 'Alamat tidak ditemukan.';
            }

            fetch(`/api/customers/${customerId}/products`)
                .then(res => res.json())
                .then(data => {
                    customerProducts = data;
                    addItemRow();
                })
                .catch(err => console.error(err));

            fetch(`/api/customers/${customerId}/invoices`)
                .then(res => res.json())
                .then(data => updateInvoiceList(data))
                .catch(err => console.error(err));
        }

        function updateInvoiceList(invoices) {
            const invoiceSelect = document.getElementById('invoice_id');
            if (!invoiceSelect) return;

            invoiceSelect.innerHTML = '<option value="">Pilih Invoice untuk menarik data...</option>';
            invoices.forEach(inv => {
                invoiceSelect.innerHTML += `<option value="${inv.id}">${inv.invoice_number}</option>`;
            });
        }

        function renumberRows() {
            const rows = document.querySelectorAll('#items-tbody tr');
            rows.forEach((row, index) => {
                const numberSpan = row.querySelector('.row-number');
                if (numberSpan) {
                    numberSpan.textContent = index + 1;
                }
            });
        }

        function addItemRowWithData(item) {
            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "group hover:bg-slate-50/50 transition-colors";

            tr.innerHTML = `
                <td class="py-3 pr-3 text-center text-slate-400 font-semibold text-xs">
                    <span class="row-number">${itemIndex + 1}</span>
                </td>
                <td class="py-3 pr-3">
                    <input type="hidden" name="items[${itemIndex}][product_id]" value="${item.product_id}" />
                    <span class="text-sm font-semibold text-slate-800">${item.product_name}</span>
                </td>
                <td class="py-3 pr-3 text-center">
                    <input type="number" name="items[${itemIndex}][qty]" value="${item.qty}" min="0.01" step="any" required class="w-20 py-2 px-2 text-sm border border-slate-200 rounded-lg text-center focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400" />
                </td>
                <td class="py-3 pr-3 text-center">
                    <span class="text-sm font-semibold text-slate-500">${item.unit}</span>
                </td>
                <td class="py-3">
                    <input type="text" name="items[${itemIndex}][notes]" value="${item.notes}" placeholder="Keterangan tambahan barang" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
            renumberRows();
        }

        function addItemRow() {
            if (customerProducts.length === 0) {
                alert("Harap pilih customer terlebih dahulu.");
                return;
            }

            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "group hover:bg-slate-50/50 transition-colors";
            
            let optionsHtml = '<option value="">Pilih produk...</option>';
            customerProducts.forEach(prod => {
                optionsHtml += `<option value="${prod.id}" data-unit="${prod.unit}">${prod.name}</option>`;
            });

            tr.innerHTML = `
                <td class="py-3 pr-3 text-center text-slate-400 font-semibold text-xs">
                    <span class="row-number">${itemIndex + 1}</span>
                </td>
                <td class="py-3 pr-3">
                    <select name="items[${itemIndex}][product_id]" required onchange="onProductSelect(${itemIndex}, this)" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all bg-white text-slate-700">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3 pr-3 text-center">
                    <input type="number" name="items[${itemIndex}][qty]" value="1" min="0.01" step="any" required class="w-20 py-2 px-2 text-sm border border-slate-200 rounded-lg text-center focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400" />
                </td>
                <td class="py-3 pr-3 text-center">
                    <span id="unit-label-${itemIndex}" class="text-sm font-semibold text-slate-500">-</span>
                </td>
                <td class="py-3 pr-3">
                    <input type="text" name="items[${itemIndex}][notes]" placeholder="Keterangan tambahan barang" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder-slate-400" />
                </td>
                <td class="py-3 text-right">
                    <button type="button" onclick="removeItemRow('${rowId}')" class="w-7 h-7 inline-flex items-center justify-center text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            itemIndex++;
            renumberRows();
        }

        function removeItemRow(rowId) {
            const tr = document.getElementById(rowId);
            if (tr) {
                tr.remove();
                renumberRows();
            }
        }

        function onProductSelect(idx, selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const unitLabel = document.getElementById(`unit-label-${idx}`);
            
            if (selectedOpt && selectedOpt.value) {
                unitLabel.textContent = selectedOpt.getAttribute('data-unit') || '-';
            } else {
                unitLabel.textContent = '-';
            }
        }
    </script>
</x-app-layout>
