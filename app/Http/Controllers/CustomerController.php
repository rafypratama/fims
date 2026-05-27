<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Brand;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Customer::with('brands')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhereHas('brands', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'brands' => 'nullable|string', // comma separated initial brands
        ]);

        $customer = Customer::create($validated);

        // Add main brand_name as a Brand record too
        Brand::create([
            'customer_id' => $customer->id,
            'name' => $customer->brand_name,
        ]);

        // Add extra brands if provided
        if ($request->filled('brands')) {
            $extraBrands = array_filter(array_map('trim', explode(',', $request->input('brands'))));
            foreach ($extraBrands as $extraBrandName) {
                if (strtolower($extraBrandName) !== strtolower($customer->brand_name)) {
                    Brand::create([
                        'customer_id' => $customer->id,
                        'name' => $extraBrandName,
                    ]);
                }
            }
        }

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $customer->load('brands');
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $oldBrandName = $customer->brand_name;
        $customer->update($validated);

        // If main brand name changed, check if we need to update/create it in Brand table
        if ($oldBrandName !== $customer->brand_name) {
            $existing = Brand::where('customer_id', $customer->id)->where('name', $oldBrandName)->first();
            if ($existing) {
                $existing->update(['name' => $customer->brand_name]);
            } else {
                Brand::create([
                    'customer_id' => $customer->id,
                    'name' => $customer->brand_name,
                ]);
            }
        }

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    // Add brand to customer (AJAX or normal POST)
    public function addBrand(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
        ]);

        // Check duplicate
        $exists = Brand::where('customer_id', $customer->id)
            ->where('name', $validated['brand_name'])
            ->exists();

        if (!$exists) {
            Brand::create([
                'customer_id' => $customer->id,
                'name' => $validated['brand_name'],
            ]);
        }

        return back()->with('success', 'Brand berhasil ditambahkan.');
    }

    // Delete brand from customer
    public function deleteBrand(Customer $customer, Brand $brand)
    {
        // Prevent deleting the main brand if it's the only one or if it matches brand_name
        if (strtolower($brand->name) === strtolower($customer->brand_name)) {
            // Check if there are other brands
            $count = Brand::where('customer_id', $customer->id)->count();
            if ($count <= 1) {
                return back()->with('error', 'Tidak dapat menghapus brand utama karena merupakan satu-satunya brand.');
            }
        }

        $brand->delete();
        return back()->with('success', 'Brand berhasil dihapus.');
    }
}
