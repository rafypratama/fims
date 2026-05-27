<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DeliveryOrder::with(['customer', 'invoice', 'creator'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sj_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('brand_name', 'like', "%{$search}%");
                  });
            });
        }

        $deliveryOrders = $query->paginate(10)->withQueryString();

        return view('delivery_orders.index', compact('deliveryOrders', 'search'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        
        // Auto-generate SJ number: SJ/YYYYMMDD/0001
        $dateStr = date('Ymd');
        $lastDO = DeliveryOrder::where('sj_number', 'like', "SJ/{$dateStr}/%")
            ->orderBy('sj_number', 'desc')
            ->first();

        if ($lastDO) {
            $parts = explode('/', $lastDO->sj_number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }
        $sjNumber = "SJ/{$dateStr}/" . str_pad($seq, 4, '0', STR_PAD_LEFT);

        // Pre-fill from invoice if provided
        $invoice = null;
        if ($request->has('invoice_id')) {
            $invoice = Invoice::with(['customer', 'items'])->find($request->input('invoice_id'));
        }

        return view('delivery_orders.create', compact('customers', 'sjNumber', 'invoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sj_number' => 'required|string|unique:delivery_orders,sj_number',
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'delivery_date' => 'required|date',
            'sender_name' => 'nullable|string|max:255',
            'receiver_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $deliveryOrder = DeliveryOrder::create([
                'sj_number' => $validated['sj_number'],
                'customer_id' => $validated['customer_id'],
                'invoice_id' => $validated['invoice_id'],
                'delivery_date' => $validated['delivery_date'],
                'sender_name' => $validated['sender_name'],
                'receiver_name' => $validated['receiver_name'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                $deliveryOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'unit' => $product->unit,
                    'notes' => $item['notes'],
                ]);
            }

            // Optional: update invoice status to 'Dikirim' if linked
            if ($deliveryOrder->invoice_id) {
                $invoice = Invoice::find($deliveryOrder->invoice_id);
                if ($invoice && $invoice->status === 'Draft') {
                    $invoice->update(['status' => 'Dikirim']);
                }
            }

            DB::commit();
            return redirect()->route('delivery-orders.index')->with('success', 'Surat Jalan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan Surat Jalan: ' . $e->getMessage());
        }
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['customer', 'items.product', 'invoice']);
        return view('delivery_orders.show', compact('deliveryOrder'));
    }

    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items');
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $products = \App\Models\Product::where('customer_id', $deliveryOrder->customer_id)->orderBy('name')->get();
        $invoices = Invoice::where('customer_id', $deliveryOrder->customer_id)->latest()->get();

        return view('delivery_orders.edit', compact('deliveryOrder', 'customers', 'products', 'invoices'));
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'sj_number' => 'required|string|unique:delivery_orders,sj_number,' . $deliveryOrder->id,
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'delivery_date' => 'required|date',
            'sender_name' => 'nullable|string|max:255',
            'receiver_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $deliveryOrder->update([
                'sj_number' => $validated['sj_number'],
                'customer_id' => $validated['customer_id'],
                'invoice_id' => $validated['invoice_id'],
                'delivery_date' => $validated['delivery_date'],
                'sender_name' => $validated['sender_name'],
                'receiver_name' => $validated['receiver_name'],
                'notes' => $validated['notes'],
            ]);

            // Re-create items
            $deliveryOrder->items()->delete();
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                $deliveryOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'unit' => $product->unit,
                    'notes' => $item['notes'],
                ]);
            }

            DB::commit();
            return redirect()->route('delivery-orders.show', $deliveryOrder->id)->with('success', 'Surat Jalan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui Surat Jalan: ' . $e->getMessage());
        }
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->delete();
        return redirect()->route('delivery-orders.index')->with('success', 'Surat Jalan berhasil dihapus.');
    }

    // PDF generation (Dot-matrix continuous form: 241mm x 140mm)
    public function downloadPdf(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['customer', 'items']);
        $settings = [
            'company_name' => SystemSetting::getByKey('company_name', 'Faacos Indonesia'),
            'company_address' => SystemSetting::getByKey('company_address', ''),
            'company_phone' => SystemSetting::getByKey('company_phone', ''),
            'signature_name' => SystemSetting::getByKey('signature_name', 'Eva Triwulandari'),
            'signature_role' => SystemSetting::getByKey('signature_role', 'Direktur Utama'),
        ];

        // Format filename: sj-[number].pdf
        $cleanNumber = str_replace('/', '-', $deliveryOrder->sj_number);
        $filename = "sj-{$cleanNumber}.pdf";

        // Setup PDF layout size (Spec: 241mm x 140mm continuous paper format)
        // 241mm = 683pt, 140mm = 397pt
        $pdf = Pdf::loadView('delivery_orders.pdf', compact('deliveryOrder', 'settings'))
            ->setPaper([0, 0, 683.0, 397.0], 'portrait');

        return $pdf->download($filename);
    }

    // Direct print view
    public function print(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['customer', 'items']);
        $settings = [
            'company_name' => SystemSetting::getByKey('company_name', 'Faacos Indonesia'),
            'company_address' => SystemSetting::getByKey('company_address', ''),
            'company_phone' => SystemSetting::getByKey('company_phone', ''),
            'signature_name' => SystemSetting::getByKey('signature_name', 'Eva Triwulandari'),
            'signature_role' => SystemSetting::getByKey('signature_role', 'Direktur Utama'),
        ];

        return view('delivery_orders.print', compact('deliveryOrder', 'settings'));
    }
}
