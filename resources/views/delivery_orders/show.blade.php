<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header action buttons -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('delivery-orders.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                    ← Kembali
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Surat Jalan: {{ $deliveryOrder->sj_number }}</h1>
                    <p class="text-xs text-slate-500 mt-1">Dibuat oleh {{ $deliveryOrder->creator->name }} pada {{ $deliveryOrder->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <!-- PDF Download -->
                <a href="{{ route('delivery-orders.pdf', $deliveryOrder->id) }}" class="inline-flex items-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    💾 Unduh PDF
                </a>
                <!-- Print View -->
                <a href="{{ route('delivery-orders.print', $deliveryOrder->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    🖨️ Cetak
                </a>
                <!-- Edit -->
                <a href="{{ route('delivery-orders.edit', $deliveryOrder->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    ✏️ Edit
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 columns: Delivery Order main details & items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Customer</span>
                            <span class="font-semibold text-slate-900 block mt-1">{{ $deliveryOrder->customer->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Brand Utama</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mt-1">
                                {{ $deliveryOrder->customer->brand_name }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Kirim</span>
                            <span class="text-slate-800 block mt-1">{{ $deliveryOrder->delivery_date->format('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Referensi Invoice</span>
                            <span class="block mt-1">
                                @if($deliveryOrder->invoice)
                                    <a href="{{ route('invoices.show', $deliveryOrder->invoice_id) }}" class="text-indigo-600 hover:underline font-semibold">
                                        {{ $deliveryOrder->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-normal italic">Manual (Tanpa Invoice)</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="font-bold text-slate-900 mb-3">Daftar Barang yang Dikirim</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 font-semibold text-slate-500 uppercase tracking-wider text-xs">
                                        <th class="pb-2">Nama Produk / Barang</th>
                                        <th class="pb-2 text-center">Satuan</th>
                                        <th class="pb-2 text-right">Qty</th>
                                        <th class="pb-2 pl-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($deliveryOrder->items as $item)
                                        <tr>
                                            <td class="py-3 font-semibold text-slate-800">{{ $item->product_name }}</td>
                                            <td class="py-3 text-center text-slate-550">{{ $item->unit }}</td>
                                            <td class="py-3 text-right text-slate-700 font-mono font-semibold">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                            <td class="py-3 pl-4 text-slate-500 text-xs">{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-slate-300">
                                        <td colspan="2" class="py-3 text-right font-bold text-slate-900">Total</td>
                                        <td class="py-3 text-right font-mono font-bold text-slate-900">{{ number_format($deliveryOrder->items->sum('qty'), 0, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 column: Courier info, internal notes -->
            <div class="space-y-6">
                <!-- Logistics Info Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">Informasi Pengiriman</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block">Sopir / Kurir:</span>
                            <span class="font-medium text-slate-800 block mt-0.5">{{ $deliveryOrder->sender_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block">Penerima Barang:</span>
                            <span class="font-medium text-slate-800 block mt-0.5">{{ $deliveryOrder->receiver_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-3">
                    <h3 class="font-bold text-slate-900 text-sm">Catatan Jalan</h3>
                    <p class="text-sm text-slate-600 whitespace-pre-line leading-relaxed">
                        {{ $deliveryOrder->notes ?? 'Tidak ada catatan khusus.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
