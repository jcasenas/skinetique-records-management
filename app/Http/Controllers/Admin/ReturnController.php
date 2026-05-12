<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'reason'        => ['nullable', 'string', 'max:255'],
            'refund_amount' => ['required', 'numeric', 'min:0'],
            'return_date'   => ['required', 'date', 'before_or_equal:today'],
        ]);

         // Guard: order must have at least one payment
        if ($order->payment_status === 'pending') {
            return back()->with('error', 'Returns can only be recorded for orders that have at least one payment recorded.');
        }

        // Guard: product must be in this order
        $orderLine = $order->orderLines->firstWhere('product_id', $request->product_id);
        if (! $orderLine) {
            return back()->with('error', 'This product is not part of the selected order.');
        }

        // Guard: can't return more than was ordered
        $alreadyReturned = $order->returns()
            ->where('product_id', $request->product_id)
            ->sum('quantity');

        if (($alreadyReturned + $request->quantity) > $orderLine->quantity) {
            return back()->with('error', 'Return quantity exceeds the quantity originally ordered.');
        }

        DB::transaction(function () use ($request, $order) {
            OrderReturn::create([
                'order_id'      => $order->id,
                'product_id'    => $request->product_id,
                'employee_id'   => Auth::guard('employee')->id(),
                'quantity'      => $request->quantity,
                'reason'        => $request->reason,
                'refund_amount' => $request->refund_amount,
                'return_date'   => $request->return_date,
            ]);

            // Put stock back
            $product = Product::findOrFail($request->product_id);
            $product->quantity += $request->quantity;
            $product->status = 'available';
            $product->save();
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Return recorded and stock restored for ' . $request->quantity . ' unit(s).');
    }
}