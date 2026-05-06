<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Payment;
use App\Models\StockIn;
use App\Models\StockAdjustment;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        // ── KPI cards ──────────────────────────────────────────
        $ordersToday = Order::whereDate('order_date', $today)
            ->whereNull('archived_at')
            ->count();

        $earnedToday = Order::whereDate('order_date', $today)
            ->whereNull('archived_at')
            ->sum('total');

        // ── Recent activity feed ───────────────────────────────
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($o) => [
                'type'   => 'order',
                'label'  => 'New order placed',
                'detail' => $o->order_label . ' — ' . ($o->customer->full_name ?? '—'),
                'amount' => '₱' . number_format($o->total, 2),
                'time'   => $o->created_at,
            ]);

        $recentPayments = Payment::with(['order', 'paymentMethod'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'type'   => 'payment',
                'label'  => 'Payment recorded',
                'detail' => ($p->order->order_label ?? '—') . ' via ' . ($p->paymentMethod->name ?? '—'),
                'amount' => '₱' . number_format($p->amount, 2),
                'time'   => $p->created_at,
            ]);

        $recentStocks = StockIn::with(['product', 'employee'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'type'   => 'stock',
                'label'  => 'Stock recorded',
                'detail' => '+' . $s->quantity . ' ' . ($s->product->name ?? '—'),
                'amount' => null,
                'time'   => $s->created_at,
            ]);

        $recentAdjustments = StockAdjustment::with(['product', 'employee'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'type'   => 'adjustment',
                'label'  => 'Stock adjusted',
                'detail' => ucfirst($a->reason) . ' — ' . ($a->product->name ?? '—')
                            . ' (' . abs($a->quantity) . ' units)',
                'amount' => null,
                'time'   => $a->created_at,
            ]);

        $recentReturns = OrderReturn::with(['order', 'product'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'type'   => 'return',
                'label'  => 'Return recorded',
                'detail' => ($r->order->order_label ?? '—') . ' — ' . ($r->product->name ?? '—')
                            . ' (' . $r->quantity . ' unit' . ($r->quantity > 1 ? 's' : '') . ')',
                'amount' => $r->refund_amount > 0
                            ? '−₱' . number_format($r->refund_amount, 2)
                            : null,
                'time'   => $r->created_at,
            ]);

        $recentActivity = $recentOrders
            ->concat($recentPayments)
            ->concat($recentStocks)
            ->concat($recentAdjustments)
            ->concat($recentReturns)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // ── Pending payments count ─────────────────────────────
        $pendingPaymentsCount = Order::whereNull('archived_at')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->count();

        // ── CHART 1: Monthly revenue — last 6 months ───────────
        // Uses archived_at so it matches Business Reports definition of a sale.
        $monthlyRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $rev   = Order::whereNotNull('archived_at')
                ->whereYear('archived_at',  $date->year)
                ->whereMonth('archived_at', $date->month)
                ->sum('total');
            $monthlyRevenue->push([
                'label'   => $date->format('M'),
                'revenue' => (float) $rev,
            ]);
        }

        // ── CHART 2: Orders by payment status (active orders) ──
        $ordersByStatus = Order::whereNull('archived_at')
            ->select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        $statusCounts = [
            'pending'    => (int) ($ordersByStatus['pending']    ?? 0),
            'partial'    => (int) ($ordersByStatus['partial']    ?? 0),
            'fully_paid' => (int) ($ordersByStatus['fully_paid'] ?? 0),
        ];

        // ── CHART 3: Top 5 products by units sold (all time) ───
        $topProducts = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty')
            )
            ->with('product:id,name')
            ->whereHas('order', fn ($q) => $q->whereNotNull('archived_at'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->product->name ?? '—',
                'qty'  => (int) $row->total_qty,
            ]);

        return view('admin.dashboard', compact(
            'ordersToday',
            'earnedToday',
            'recentActivity',
            'pendingPaymentsCount',
            'monthlyRevenue',
            'statusCounts',
            'topProducts',
        ));
    }
}