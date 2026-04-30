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

        DB::transaction(function () use ($request) {
            StockAdjustment::create([
                'product_id'      => $request->product_id,
                'employee_id'     => Auth::guard('employee')->id(),
                'quantity'        => -abs($request->quantity), // always stored negative
                'reason'          => $request->reason,
                'notes'           => $request->notes,
                'adjustment_date' => $request->adjustment_date,
            ]);

            $product = Product::findOrFail($request->product_id);
            $product->quantity = max(0, $product->quantity - abs($request->quantity));
            if ($product->quantity === 0) {
                $product->status = 'unavailable';
            }
            $product->save();
        });

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock adjustment recorded. Product quantity updated.');
    }
}