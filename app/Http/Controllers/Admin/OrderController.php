<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'deliveryMethod'])
            ->whereNull('archived_at')
            ->latest('order_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name',  'like', "%{$search}%");
                  });
            });
        }

        $orders          = $query->paginate(10)->withQueryString();
        $customers       = Customer::orderBy('first_name')->get();
        $products        = Product::where('status', 'available')->orderBy('name')->get();
        $deliveryMethods = DeliveryMethod::all();

        // Use the actual last DB id so the preview label is always accurate,
        // regardless of how many orders are archived or filtered out.
        $lastId      = Order::max('id') ?? 0;
        $nextOrderId = 'O' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $openModal = $request->boolean('open');

        return view('admin.orders.index', compact(
            'orders',
            'customers',
            'products',
            'deliveryMethods',
            'nextOrderId',
            'openModal',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id'           => ['required', 'exists:customers,id'],
            'delivery_method_id'    => ['required', 'exists:delivery_methods,id'],
            'delivery_fee'          => ['required', 'numeric', 'min:0'],
            'products'              => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity'   => ['required', 'integer', 'min:1'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'products.required'           => 'Please add at least one product to the cart.',
            'customer_id.required'        => 'Please select a customer.',
            'delivery_method_id.required' => 'Please select a delivery method.',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = collect($request->products)->sum(
                fn($line) => $line['quantity'] * $line['unit_price']
            );

            $deliveryFee = $request->delivery_fee ?? 0;

            $order = Order::create([
                'customer_id'        => $request->customer_id,
                'delivery_method_id' => $request->delivery_method_id,
                'order_date'         => now()->toDateString(),
                'subtotal'           => $subtotal,
                'delivery_fee'       => $deliveryFee,
                'total'              => $subtotal + $deliveryFee,
                'payment_status'     => 'pending',
            ]);

            foreach ($request->products as $line) {
                OrderLine::create([
                    'order_id'   => $order->id,
                    'product_id' => $line['product_id'],
                    'quantity'   => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['quantity'] * $line['unit_price'],
                ]);
            }
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function archive(Order $order): RedirectResponse
    {
        // Guard: only fully paid orders can be archived
        if ($order->payment_status !== 'fully_paid') {
            return back()->with('error', 'Only fully paid orders can be archived.');
        }

        // Guard: already archived
        if ($order->archived_at) {
            return back()->with('error', 'This order has already been archived.');
        }

        DB::transaction(function () use ($order) {
            // Load order lines, their products, and any returns already recorded
            // so we can calculate the net quantity to deduct per product.
            $order->load('orderLines.product', 'returns');

            foreach ($order->orderLines as $line) {
                // Subtract units already returned — their stock was restored
                // when the return was recorded, so we must not deduct them again.
                $alreadyReturned = $order->returns
                    ->where('product_id', $line->product_id)
                    ->sum('quantity');

                $netQty = max(0, $line->quantity - $alreadyReturned);

                $product = $line->product;
                $product->quantity = max(0, $product->quantity - $netQty);
                if ($product->quantity === 0) {
                    $product->status = 'unavailable';
                }
                $product->save();
            }

            $order->update(['archived_at' => now()]);
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order ' . $order->order_label . ' has been archived and product stock updated.');
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'deliveryMethod',
            'orderLines.product',
            'payments.paymentMethod',
            'payments.receipt',
            'returns.product',
            'returns.employee',
        ]);

        dd($order->orderLines, $order->id);

        $totalPaid      = $order->payments->sum('amount');
        $totalRemaining = $order->total - $totalPaid;

        return view('admin.orders.show', compact('order', 'totalPaid', 'totalRemaining'));
    }

    public function archives(Request $request): View
    {
        $query = Order::with(['customer', 'deliveryMethod'])
            ->whereNotNull('archived_at')
            ->latest('archived_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name',  'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.archives', compact('orders'));
    }
}