<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'payments'])
            ->whereNull('archived_at')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($q2) =>
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name',  'like', "%{$search}%")
                  );
            });
        }

        $pendingOrders  = $query->paginate(10)->withQueryString();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('admin.payments.index', compact('pendingOrders', 'paymentMethods'));
    }

    public function history(Request $request): View
    {
        $query = Order::with(['customer', 'payments.paymentMethod', 'payments.receipt'])
            ->where('payment_status', 'fully_paid')
            ->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($q2) =>
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name',  'like', "%{$search}%")
                  );
            });
        }

        $paidOrders = $query->paginate(10)->withQueryString();

        return view('admin.payments.history', compact('paidOrders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id'          => ['required', 'exists:orders,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
        ]);

        $order = Order::findOrFail($request->order_id);

        // Re-query live balance — do not rely on a value computed earlier in the request
        // to guard against concurrent payments on the same order.
        $paid      = $order->payments()->sum('amount');
        $remaining = $order->total - $paid;

        if ($request->amount > $remaining) {
            return back()->withErrors([
                'amount' => 'Amount exceeds remaining balance of PHP ' . number_format($remaining, 2),
            ])->withInput();
        }

        $payment = DB::transaction(function () use ($request, $order) {
            $payment = Payment::create([
                'order_id'          => $order->id,
                'payment_method_id' => $request->payment_method_id,
                'amount'            => $request->amount,
                'payment_date'      => now()->toDateString(),
            ]);

            // Auto-generate receipt
            $receiptNum = 'RCP-' . strtoupper(substr(md5($payment->id . now()->timestamp), 0, 8));
            Receipt::create([
                'payment_id'  => $payment->id,
                'receipt_num' => $receiptNum,
                'issued_at'   => now(),
            ]);

            // Re-query inside the transaction so the status update reflects
            // all payments committed so far, including this one.
            $newPaid = $order->payments()->sum('amount');
            if ($newPaid >= $order->total) {
                $order->update(['payment_status' => 'fully_paid']);
            } elseif ($newPaid > 0) {
                $order->update(['payment_status' => 'partial']);
            }

            return $payment;
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded. Receipt ' . $payment->receipt->receipt_num . ' generated successfully.');
    }

    // ── Download receipt as PDF ──────────────────────────────────
    public function receipt(Payment $payment)
    {
        $payment->load([
            'receipt',
            'paymentMethod',
            'order.customer',
            'order.deliveryMethod',
            'order.orderLines.product',
            'order.payments.paymentMethod',
            'order.payments.receipt',
        ]);

        if (! $payment->receipt) {
            abort(404, 'Receipt not found for this payment.');
        }

        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('receipt-' . $payment->receipt->receipt_num . '.pdf');
    }
}