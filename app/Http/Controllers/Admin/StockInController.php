<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockIn::with(['product', 'supplier', 'employee'])
            ->latest('stock_in_date')
            ->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', fn ($q2) =>
                      $q2->where('name', 'like', "%{$search}%")
                  )
                  ->orWhereHas('supplier', fn ($q2) =>
                      $q2->where('name', 'like', "%{$search}%")
                  );
            });
        }

        // Only show approved records in the history tab
        $stockIns = (clone $query)
            ->where('status', 'approved')
            ->paginate(12)
            ->withQueryString();

        // Pending stock-ins (owner review queue)
        $pendingStockIns = StockIn::with(['product', 'supplier', 'employee'])
            ->where('status', 'pending')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        // Rejected stock-ins
        $rejectedStockIns = StockIn::with(['product', 'supplier', 'employee'])
            ->where('status', 'rejected')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        // Approved adjustments shown in the Adjustments history tab
        $adjustments = StockAdjustment::with(['product', 'employee'])
            ->where('status', 'approved')
            ->latest('adjustment_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        // Pending adjustments for owner approval queue
        $pendingAdjustments = StockAdjustment::with(['product', 'employee'])
            ->where('status', 'pending')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        // Rejected adjustments
        $rejectedAdjustments = StockAdjustment::with(['product', 'employee'])
            ->where('status', 'rejected')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.stocks.index', compact(
            'stockIns',
            'pendingStockIns',
            'rejectedStockIns',
            'products',
            'suppliers',
            'adjustments',
            'pendingAdjustments',
            'rejectedAdjustments',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'stock_in_date' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'quantity.min'                  => 'Quantity must be at least 1.',
            'stock_in_date.before_or_equal' => 'Stock-in date cannot be in the future.',
        ]);

        StockIn::create([
            'product_id'    => $request->product_id,
            'supplier_id'   => $request->supplier_id,
            'employee_id'   => Auth::guard('employee')->id(),
            'quantity'      => $request->quantity,
            'stock_in_date' => $request->stock_in_date,
            'status'        => 'pending',
        ]);

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock-in submitted and is awaiting owner approval.');
    }

    public function approve(StockIn $stockIn): RedirectResponse
    {
        if (! $stockIn->isPending()) {
            return back()->with('error', 'This stock-in has already been actioned.');
        }

        DB::transaction(function () use ($stockIn) {
            $stockIn->update(['status' => 'approved']);

            $product = $stockIn->product;
            $product->quantity += $stockIn->quantity;
            if ($product->quantity > 0) {
                $product->status = 'available';
            }
            $product->save();
        });

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock-in approved. Product quantity updated.');
    }

    public function reject(Request $request, StockIn $stockIn): RedirectResponse
    {
        if (! $stockIn->isPending()) {
            return back()->with('error', 'This stock-in has already been actioned.');
        }

        $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:255'],
        ]);

        $stockIn->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock-in rejected.');
    }
}