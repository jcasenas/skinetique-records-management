<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id'      => ['required', 'exists:products,id'],
            'quantity'        => ['required', 'integer', 'min:1'],
            'reason'          => ['required', 'in:damaged,lost,expired,correction'],
            'notes'           => ['nullable', 'string', 'max:255'],
            'adjustment_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        // Save as pending — quantity is NOT deducted until owner approves
        StockAdjustment::create([
            'product_id'      => $request->product_id,
            'employee_id'     => Auth::guard('employee')->id(),
            'quantity'        => -abs($request->quantity),
            'reason'          => $request->reason,
            'notes'           => $request->notes,
            'adjustment_date' => $request->adjustment_date,
            'status'          => 'pending',
        ]);

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock adjustment submitted and is awaiting owner approval.');
    }

    public function approve(StockAdjustment $adjustment): RedirectResponse
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'This adjustment has already been actioned.');
        }

        DB::transaction(function () use ($adjustment) {
            $adjustment->update(['status' => 'approved']);

            $product = $adjustment->product;
            $product->quantity = max(0, $product->quantity - abs($adjustment->quantity));
            if ($product->quantity === 0) {
                $product->status = 'unavailable';
            }
            $product->save();
        });

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Adjustment approved. Product quantity updated.');
    }

    public function reject(Request $request, StockAdjustment $adjustment): RedirectResponse
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'This adjustment has already been actioned.');
        }

        $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:255'],
        ]);

        $adjustment->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Adjustment rejected.');
    }
}