<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header action buttons -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" onclick="if(document.referrer) { window.history.back(); return false; }" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                    ← Kembali
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Detail Invoice: {{ $invoice->invoice_number }}</h1>
                    <p class="text-xs text-slate-500 mt-1">Dibuat oleh {{ $invoice->creator->name }} pada {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <!-- PDF Download -->
                <a href="{{ route('invoices.pdf', $invoice->id) }}" class="inline-flex items-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    💾 Unduh PDF
                </a>
                <!-- Print View -->
                <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    🖨️ Cetak
                </a>
                <!-- Edit -->
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm">
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
            <!-- Left 2 columns: Invoice main details & items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Invoice general card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Customer</span>
                            <span class="font-semibold text-slate-900 block mt-1">{{ $invoice->customer->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Brand Utama</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mt-1">
                                {{ $invoice->customer->brand_name }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Invoice</span>
                            <span class="text-slate-800 block mt-1">{{ $invoice->date->format('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Jatuh Tempo</span>
                            <span class="text-slate-800 block mt-1">{{ $invoice->due_date ? $invoice->due_date->format('d F Y') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="font-bold text-slate-900 mb-3">Item Tagihan</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 font-semibold text-slate-500 uppercase tracking-wider text-xs">
                                        <th class="pb-2">Deskripsi Produk</th>
                                        <th class="pb-2 text-center">Satuan</th>
                                        <th class="pb-2 text-right">Qty</th>
                                        <th class="pb-2 text-right">Harga Jual</th>
                                        <th class="pb-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td class="py-3 font-semibold text-slate-800">{{ $item->product_name }}</td>
                                            <td class="py-3 text-center text-slate-550">{{ $item->unit }}</td>
                                            <td class="py-3 text-right text-slate-700 font-mono">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                            <td class="py-3 text-right text-slate-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="py-3 text-right font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Linked Delivery Orders (Surat Jalan) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">Surat Jalan Terkait (Delivery Orders)</h3>
                        <a href="{{ route('delivery-orders.create', ['invoice_id' => $invoice->id]) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-750 font-semibold text-xs rounded-lg border border-indigo-100 transition-colors">
                            + Buat Surat Jalan Baru
                        </a>
                    </div>
                    @if($invoice->deliveryOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 font-semibold text-slate-400 text-xs">
                                        <th class="pb-2">No. Surat Jalan</th>
                                        <th class="pb-2">Tanggal Kirim</th>
                                        <th class="pb-2">Penerima</th>
                                        <th class="pb-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-slate-700">
                                    @foreach($invoice->deliveryOrders as $do)
                                        <tr>
                                            <td class="py-2.5 font-medium text-indigo-600 hover:underline">
                                                <a href="{{ route('delivery-orders.show', $do->id) }}">{{ $do->sj_number }}</a>
                                            </td>
                                            <td class="py-2.5 text-slate-500">{{ $do->delivery_date->format('d/m/Y') }}</td>
                                            <td class="py-2.5 text-slate-650">{{ $do->receiver_name ?? '-' }}</td>
                                            <td class="py-2.5 text-right">
                                                <a href="{{ route('delivery-orders.show', $do->id) }}" class="text-xs text-indigo-600 hover:underline">
                                                    Detail →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Belum ada surat jalan yang terbuat dari invoice ini.</p>
                    @endif
                </div>
            </div>

            <!-- Right 1 column: Summary calculation status, notes -->
            <div class="space-y-6">
                <!-- Status & Totals Summary Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="text-center pb-4 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Invoice</span>
                        <div class="mt-1.5">
                            @php
                                $badgeClasses = [
                                    'Draft' => 'bg-slate-100 text-slate-800',
                                    'Dikirim' => 'bg-indigo-50 text-indigo-700 border border-indigo-100',
                                    'Dibayar Sebagian' => 'bg-blue-100 text-blue-800',
                                    'Lunas' => 'bg-emerald-100 text-emerald-800',
                                    'Jatuh Tempo' => 'bg-red-100 text-red-800',
                                    'Dibatalkan' => 'bg-gray-100 text-gray-800',
                                ];
                                $class = $badgeClasses[$invoice->status] ?? 'bg-slate-100 text-slate-800';
                            @endphp
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold {{ $class }}">
                                {{ $invoice->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Financial calculation breakdown -->
                    <div class="space-y-3 text-sm text-slate-650">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>PPN (11%):</span>
                            <span class="font-medium text-slate-800">
                                @if($invoice->use_ppn)
                                    Rp {{ number_format($invoice->subtotal * 0.11, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Uang Muka (DP):</span>
                            <span class="font-medium text-slate-850">Rp {{ number_format($invoice->dp_amount, 0, ',', '.') }} ({{ round($invoice->dp_percent) }}%)</span>
                        </div>
                        <div class="border-t border-slate-100 pt-3 flex justify-between font-bold text-lg text-slate-900">
                            <span>Tagihan Net:</span>
                            <span class="text-indigo-600">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-3">
                    <h3 class="font-bold text-slate-900 text-sm">Catatan Internal / Syarat</h3>
                    <p class="text-sm text-slate-600 whitespace-pre-line leading-relaxed">
                        {{ $invoice->notes ?? 'Tidak ada catatan untuk invoice ini.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
