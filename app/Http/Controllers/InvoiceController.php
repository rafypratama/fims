<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\BankAccount;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $statusGroup = $request->input('status_group');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Invoice::with(['customer', 'creator', 'deliveryOrders'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('brand_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($statusGroup === 'unpaid') {
            $query->whereIn('status', ['Draft', 'Dikirim', 'Dibayar Sebagian']);
        } elseif ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $invoices = $query->paginate(10)->withQueryString();

        return view('invoices.index', compact('invoices', 'search', 'status', 'statusGroup', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        
        // Auto-generate invoice number: INV/YYYYMMDD/0001
        $dateStr = date('Ymd');
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV/{$dateStr}/%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }
        $invoiceNumber = "INV/{$dateStr}/" . str_pad($seq, 4, '0', STR_PAD_LEFT);

        return view('invoices.create', compact('customers', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $customerId = $request->input('customer_id');
        
        // Intercept and auto-create customer if NEW: prefixed
        if (is_string($customerId) && str_starts_with($customerId, 'NEW:')) {
            $newCustomerName = substr($customerId, 4);
            $customer = \App\Models\Customer::firstOrCreate(
                ['name' => $newCustomerName],
                [
                    'brand_name' => $newCustomerName,
                    'pic' => '-',
                    'phone' => '-',
                    'email' => strtolower(str_replace(' ', '', $newCustomerName)) . '@example.com',
                    'address' => 'Dibuat otomatis via verifikasi PDF',
                ]
            );
            $customerId = $customer->id;
            $request->merge(['customer_id' => $customerId]);
        }

        // Intercept and auto-create products if NEW: prefixed
        if ($request->has('items') && is_array($request->input('items'))) {
            $items = $request->input('items');
            foreach ($items as $index => $item) {
                if (isset($item['product_id']) && is_string($item['product_id']) && str_starts_with($item['product_id'], 'NEW:')) {
                    $newProductName = substr($item['product_id'], 4);
                    $product = \App\Models\Product::firstOrCreate(
                        [
                            'customer_id' => $customerId,
                            'name' => $newProductName
                        ],
                        [
                            'code' => null,
                            'unit' => $item['unit'] ?? 'Pcs',
                            'default_price' => $item['unit_price'] ?? 0,
                        ]
                    );
                    $items[$index]['product_id'] = $product->id;
                }
            }
            $request->merge(['items' => $items]);
        }

        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Draft,PO,Dikirim,Dibayar Sebagian,Lunas,Jatuh Tempo,Dibatalkan',
            'dp_amount' => 'nullable|numeric|min:0',
            'dp_percent' => 'nullable|numeric|min:0|max:100',
            'use_ppn' => 'boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                // Get product name snapshot
                $product = \App\Models\Product::find($item['product_id']);
                $itemSubtotal = $item['qty'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'unit' => $product->unit,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $ppn = $request->has('use_ppn') && $request->input('use_ppn') ? ($subtotal * 0.11) : 0;
            $totalBeforeDp = $subtotal + $ppn;
            
            // Handle DP
            $dpAmount = $validated['dp_amount'] ?? 0;
            $dpPercent = $validated['dp_percent'] ?? 0;
            if ($dpPercent > 0) {
                $dpAmount = ($totalBeforeDp * $dpPercent) / 100;
            }

            $grandTotal = max(0, $totalBeforeDp - $dpAmount);

            $invoice = Invoice::create([
                'invoice_number' => $validated['invoice_number'],
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'dp_amount' => $dpAmount,
                'dp_percent' => $dpPercent,
                'use_ppn' => $request->has('use_ppn'),
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice berhasil disimpan! Anda dapat membuat Surat Jalan dari halaman ini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'deliveryOrders']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::where('status', true)->orderBy('name')->get();
        // Load products for the selected customer
        $products = \App\Models\Product::where('customer_id', $invoice->customer_id)->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'customers', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Draft,PO,Dikirim,Dibayar Sebagian,Lunas,Jatuh Tempo,Dibatalkan',
            'dp_amount' => 'required|numeric|min:0',
            'dp_percent' => 'required|numeric|min:0|max:100',
            'use_ppn' => 'boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                $itemSubtotal = $item['qty'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'unit' => $product->unit,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $ppn = $request->has('use_ppn') && $request->input('use_ppn') ? ($subtotal * 0.11) : 0;
            $totalBeforeDp = $subtotal + $ppn;
            
            $dpAmount = $validated['dp_amount'];
            if ($validated['dp_percent'] > 0) {
                $dpAmount = ($totalBeforeDp * $validated['dp_percent']) / 100;
            }

            $grandTotal = max(0, $totalBeforeDp - $dpAmount);

            $invoice->update([
                'invoice_number' => $validated['invoice_number'],
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'dp_amount' => $dpAmount,
                'dp_percent' => $validated['dp_percent'],
                'use_ppn' => $request->has('use_ppn'),
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'notes' => $validated['notes'],
            ]);

            // Re-create items
            $invoice->items()->delete();
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui invoice: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }

    // PDF generation
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        $bankAccounts = BankAccount::all();
        $settings = [
            'company_name' => SystemSetting::getByKey('company_name', 'Faacos Indonesia'),
            'company_address' => SystemSetting::getByKey('company_address', ''),
            'company_phone' => SystemSetting::getByKey('company_phone', ''),
            'signature_name' => SystemSetting::getByKey('signature_name', 'Eva Triwulandari'),
            'signature_role' => SystemSetting::getByKey('signature_role', 'Direktur Utama'),
        ];

        // Format filename: invoice-[number].pdf
        $cleanNumber = str_replace('/', '-', $invoice->invoice_number);
        $filename = "invoice-{$cleanNumber}.pdf";

        // Setup PDF layout size (Spec: 241mm x 140mm continuous paper format)
        // Convert mm to points (1mm = 2.83465pt)
        // 241mm = 683pt, 140mm = 397pt
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'bankAccounts', 'settings'))
            ->setPaper([0, 0, 683.0, 397.0], 'portrait');

        return $pdf->download($filename);
    }

    // Direct Browser Print
    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        $bankAccounts = BankAccount::all();
        $settings = [
            'company_name' => SystemSetting::getByKey('company_name', 'Faacos Indonesia'),
            'company_address' => SystemSetting::getByKey('company_address', ''),
            'company_phone' => SystemSetting::getByKey('company_phone', ''),
            'signature_name' => SystemSetting::getByKey('signature_name', 'Eva Triwulandari'),
            'signature_role' => SystemSetting::getByKey('signature_role', 'Direktur Utama'),
        ];

        return view('invoices.print', compact('invoice', 'bankAccounts', 'settings'));
    }

    // API endpoint for retrieving invoices of a customer
    public function getInvoicesByCustomer(Customer $customer)
    {
        $invoices = $customer->invoices()->latest()->get();
        return response()->json($invoices);
    }
}
