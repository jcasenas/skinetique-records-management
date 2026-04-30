<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('supplier')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($q2) =>
                      $q2->where('name', 'like', "%{$search}%")
                  );
            });
        }

        $products  = $query->paginate(10)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
        ]);

        Product::create([
            'supplier_id' => $request->supplier_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'quantity'    => 0,
            'status'      => 'unavailable',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
        ]);

        $product->update([
            'supplier_id' => $request->supplier_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Prevent deletion if product has order lines
        if ($product->orderLines()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete a product that has existing orders.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}