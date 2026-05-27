<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('delivery-orders.show', $deliveryOrder->id) }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Surat Jalan: {{ $deliveryOrder->sj_number }}</h1>
                <p class="text-sm text-slate-500">Perbarui spesifikasi pengiriman dan detail barang.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('delivery-orders.update', $deliveryOrder->id) }}" id="do-form" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Form Fields Grid -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- SJ Number -->
                <div class="space-y-1">
                    <label for="sj_number" class="text-sm font-semibold text-slate-700">Nomor Surat Jalan <span class="text-rose-500">*</span></label>
                    <input type="text" id="sj_number" name="sj_number" value="{{ old('sj_number', $deliveryOrder->sj_number) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>

                <!-- Customer Selection (Locked during edit) -->
                <div class="space-y-1">
                    <label for="customer_id" class="text-sm font-semibold text-slate-700">Customer <span class="text-rose-500">*</span></label>
                    <select id="customer_id" name="customer_id" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-slate-50 pointer-events-none">
                        <option value="{{ $deliveryOrder->customer_id }}">{{ $deliveryOrder->customer->name }}</option>
                    </select>
                </div>

                <!-- Invoice ID Link (Locked / Read-only) -->
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-700">Link Ke Invoice</label>
                    <input type="text" readonly value="{{ $deliveryOrder->invoice ? $deliveryOrder->invoice->invoice_number : 'Manual (Tanpa Invoice)' }}" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 font-semibold" />
                    <input type="hidden" name="invoice_id" value="{{ $deliveryOrder->invoice_id }}" />
                </div>

                <!-- Delivery Date -->
                <div class="space-y-1">
                    <label for="delivery_date" class="text-sm font-semibold text-slate-700">Tanggal Kirim <span class="text-rose-500">*</span></label>
                    <input type="date" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $deliveryOrder->delivery_date->format('Y-m-d')) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>

                <!-- Sender Name -->
                <div class="space-y-1">
                    <label for="sender_name" class="text-sm font-semibold text-slate-700">Nama Pengirim (Sopir / Kurir)</label>
                    <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name', $deliveryOrder->sender_name) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>

                <!-- Receiver Name -->
                <div class="space-y-1">
                    <label for="receiver_name" class="text-sm font-semibold text-slate-700">Nama Penerima</label>
                    <input type="text" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', $deliveryOrder->receiver_name) }}" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
            </div>

            <!-- Items Section -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Barang Dikirim</h2>
                    @if(!$deliveryOrder->invoice_id)
                        <button type="button" onclick="addItemRow()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-lg transition-colors border border-indigo-100">
                            + Tambah Baris
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="items-table">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <th class="pb-2 pr-4 w-1/3">Produk</th>
                                <th class="pb-2 pr-4 w-16 text-center">Satuan</th>
                                <th class="pb-2 pr-4 w-24">Qty Dikirim</th>
                                <th class="pb-2 pr-4">Keterangan Item</th>
                                @if(!$deliveryOrder->invoice_id)
                                    <th class="pb-2 text-right w-12"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="items-tbody">
                            <!-- Items inserted here by JS on load -->
                        </tbody>
                    </table>
                </div>

                <!-- Notes Section -->
                <div class="border-t border-slate-150 pt-4">
                    <div class="space-y-1 max-w-xl">
                        <label for="notes" class="text-sm font-semibold text-slate-700">Catatan Pengiriman</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('notes', $deliveryOrder->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('delivery-orders.show', $deliveryOrder->id) }}" class="px-4 py-2 border border-slate-200 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition-colors">
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
        const existingItems = @json($deliveryOrder->items);

        window.addEventListener('DOMContentLoaded', () => {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItemRowWithData(item);
                });
            } else {
                addItemRow();
            }
        });

        function addItemRowWithData(item) {
            const tbody = document.getElementById('items-tbody');
            const rowId = `item-row-${itemIndex}`;
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "hover:bg-slate-50/50 transition-colors";

            if ({{ $deliveryOrder->invoice_id ? 'true' : 'false' }}) {
                // If linked to an invoice, layout is read-only for items to prevent mismatching product catalogue
                tr.innerHTML = `
                    <td class="py-3 pr-4">
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${item.product_id}" />
                        <span class="text-sm font-semibold text-slate-800">${item.product_name}</span>
                    </td>
                    <td class="py-3 pr-4 text-center">
                        <span class="text-sm font-semibold text-slate-500">${item.unit}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <input type="number" name="items[${itemIndex}][qty]" value="${item.qty}" min="0.01" step="any" required class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                    </td>
                    <td class="py-3">
                        <input type="text" name="items[${itemIndex}][notes]" value="${item.notes || ''}" placeholder="Keterangan tambahan" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                    </td>
                `;
            } else {
                let optionsHtml = '<option value="">-- Pilih Produk --</option>';
                customerProducts.forEach(prod => {
                    const selected = prod.id == item.product_id ? 'selected' : '';
                    optionsHtml += `<option value="${prod.id}" data-unit="${prod.unit}" ${selected}>${prod.name} (${prod.code || '-'})</option>`;
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
                        <input type="number" name="items[${itemIndex}][qty]" value="${item.qty}" min="0.01" step="any" required class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                    </td>
                    <td class="py-3 pr-4">
                        <input type="text" name="items[${itemIndex}][notes]" value="${item.notes || ''}" placeholder="Keterangan tambahan" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                    </td>
                    <td class="py-3 text-right">
                        <button type="button" onclick="removeItemRow('${rowId}')" class="p-1 text-rose-600 hover:bg-rose-50 rounded-md">
                            🗑️
                        </button>
                    </td>
                `;
            }

            tbody.appendChild(tr);
            itemIndex++;
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
            tr.className = "hover:bg-slate-50/50 transition-colors";
            
            let optionsHtml = '<option value="">-- Pilih Produk --</option>';
            customerProducts.forEach(prod => {
                optionsHtml += `<option value="${prod.id}" data-unit="${prod.unit}">${prod.name} (${prod.code || '-'})</option>`;
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
                    <input type="number" name="items[${itemIndex}][qty]" value="1" min="0.01" step="any" required class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg text-right focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="py-3 pr-4">
                    <input type="text" name="items[${itemIndex}][notes]" placeholder="Keterangan tambahan" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
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
