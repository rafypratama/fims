<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customerId = $request->input('customer_id');

        $query = Product::with('customer')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $products = $query->paginate(10)->withQueryString();
        $customers = Customer::where('status', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'customers', 'search', 'customerId'));
    }

    public function create()
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        return view('products.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'default_price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        return view('products.edit', compact('product', 'customers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'default_price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    // API endpoint for Smart Autofill
    public function getProductsByCustomer(Customer $customer)
    {
        $products = $customer->products()->orderBy('name')->get();
        return response()->json($products);
    }
}
