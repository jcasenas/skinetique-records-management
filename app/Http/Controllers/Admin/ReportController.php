<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderReturn;
use App\Models\StockAdjustment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // ── Index: show the reports dashboard ──────────────────────
    public function index(): View
    {
        $currentYear  = now()->year;
        $currentMonth = now()->month;

        // A "sale" is defined as an archived (fulfilled) order.
        $totalRevenue = Order::whereNotNull('archived_at')
            ->whereYear('archived_at', $currentYear)
            ->sum('total');

        $totalOrders = Order::whereNotNull('archived_at')
            ->whereYear('archived_at', $currentYear)
            ->count();

        $totalCustomers = Customer::count();

        // ── NEW: Returns summary for current year ──────────────
        $totalReturnedUnits = OrderReturn::whereYear('return_date', $currentYear)
            ->sum('quantity');

        $totalRefunded = OrderReturn::whereYear('return_date', $currentYear)
            ->sum('refund_amount');

        $totalReturnTransactions = OrderReturn::whereYear('return_date', $currentYear)
            ->count();

        // ── NEW: Stock adjustments summary for current year ────
        $totalAdjustedUnits = StockAdjustment::whereYear('adjustment_date', $currentYear)
            ->sum(DB::raw('ABS(quantity)'));

        $totalAdjustmentTransactions = StockAdjustment::whereYear('adjustment_date', $currentYear)
            ->count();

        // Breakdown by reason
        $adjustmentsByReason = StockAdjustment::select(
                'reason',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(ABS(quantity)) as total_units')
            )
            ->whereYear('adjustment_date', $currentYear)
            ->groupBy('reason')
            ->get()
            ->keyBy('reason');

        // Recent returns preview (top 5 most recent)
        $recentReturns = OrderReturn::with(['order.customer', 'product'])
            ->whereYear('return_date', $currentYear)
            ->latest('return_date')
            ->limit(5)
            ->get();

        // Monthly breakdown — archived orders only
        $monthlySales = Order::select(
                DB::raw('MONTH(archived_at) as month'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $currentYear)
            ->groupBy(DB::raw('MONTH(archived_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Top 5 bestselling products — from archived orders only
        $bestsellers = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(line_total) as total_revenue')
            )
            ->with('product')
            ->whereHas('order', fn ($q) => $q->whereNotNull('archived_at'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Top 5 most frequent customers — from archived orders only
        $frequentCustomers = Order::select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent')
            )
            ->with('customer')
            ->whereNotNull('archived_at')
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'currentYear',
            'currentMonth',
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalReturnedUnits',
            'totalRefunded',
            'totalReturnTransactions',
            'totalAdjustedUnits',
            'totalAdjustmentTransactions',
            'adjustmentsByReason',
            'recentReturns',
            'monthlySales',
            'bestsellers',
            'frequentCustomers',
        ));
    }

    // ── PDF: Monthly Sales Report ───────────────────────────────
    public function exportMonthlySales(Request $request)
    {
        $year  = $request->input('year',  now()->year);
        $month = $request->input('month', now()->month);

        $orders = Order::with(['customer', 'deliveryMethod', 'orderLines.product'])
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $year)
            ->whereMonth('archived_at', $month)
            ->orderBy('archived_at')
            ->get();

        $totalRevenue  = $orders->sum('total');
        $totalOrders   = $orders->count();
        $totalDelivery = $orders->sum('delivery_fee');
        $totalSubtotal = $orders->sum('subtotal');

        $byStatus = $orders->groupBy('payment_status')->map->count();

        $monthName = now()->setDate($year, $month, 1)->format('F Y');

        $pdf = Pdf::loadView('admin.reports.pdf.monthly-sales', compact(
            'orders', 'totalRevenue', 'totalOrders',
            'totalDelivery', 'totalSubtotal', 'byStatus',
            'monthName', 'year', 'month',
        ))->setPaper('a4', 'landscape');

        return $pdf->download("skinetique-monthly-sales-{$year}-{$month}.pdf");
    }

    // ── PDF: Bestselling Products ───────────────────────────────
    public function exportBestsellers(Request $request)
    {
        $year = $request->input('year', now()->year);

        $products = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_price')
            )
            ->with('product.supplier')
            ->whereHas('order', fn ($q) =>
                $q->whereNotNull('archived_at')->whereYear('archived_at', $year)
            )
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();

        $grandTotalQty     = $products->sum('total_qty');
        $grandTotalRevenue = $products->sum('total_revenue');

        $pdf = Pdf::loadView('admin.reports.pdf.bestsellers', compact(
            'products', 'grandTotalQty', 'grandTotalRevenue', 'year',
        ))->setPaper('a4', 'portrait');

        return $pdf->download("skinetique-bestsellers-{$year}.pdf");
    }

    // ── PDF: Frequent Customers ─────────────────────────────────
    public function exportFrequentCustomers(Request $request)
    {
        $year = $request->input('year', now()->year);

        $customers = Order::select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent'),
                DB::raw('AVG(total) as avg_order_value'),
                DB::raw('MAX(archived_at) as last_order_date')
            )
            ->with('customer')
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $year)
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->get();

        $grandTotalOrders = $customers->sum('order_count');
        $grandTotalSpent  = $customers->sum('total_spent');

        $pdf = Pdf::loadView('admin.reports.pdf.frequent-customers', compact(
            'customers', 'grandTotalOrders', 'grandTotalSpent', 'year',
        ))->setPaper('a4', 'portrait');

        return $pdf->download("skinetique-frequent-customers-{$year}.pdf");
    }

    // ── PDF: Full Annual Summary ────────────────────────────────
    public function exportAnnualSummary(Request $request)
    {
        $year = $request->input('year', now()->year);

        $monthlySales = Order::select(
                DB::raw('MONTH(archived_at) as month'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(delivery_fee) as delivery_fee'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $year)
            ->groupBy(DB::raw('MONTH(archived_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $annualRevenue = $monthlySales->sum('revenue');
        $annualOrders  = $monthlySales->sum('order_count');

        $topProducts = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(line_total) as total_revenue')
            )
            ->with('product')
            ->whereHas('order', fn ($q) =>
                $q->whereNotNull('archived_at')->whereYear('archived_at', $year)
            )
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topCustomers = Order::select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent')
            )
            ->with('customer')
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $year)
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.annual-summary', compact(
            'year', 'monthlySales', 'annualRevenue', 'annualOrders',
            'topProducts', 'topCustomers',
        ))->setPaper('a4', 'portrait');

        return $pdf->download("skinetique-annual-summary-{$year}.pdf");
    }
}