<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Customer;
use App\Models\StockAdjustment;
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

        // Summary cards
        $totalRevenue = Order::whereNotNull('archived_at')
            ->whereYear('archived_at', $currentYear)
            ->sum('total');

        $totalOrders = Order::whereNotNull('archived_at')
            ->whereYear('archived_at', $currentYear)
            ->count();

        $totalCustomers = Customer::count();

        // Monthly sales for current year
        $monthlySales = Order::select(
                DB::raw('MONTH(order_date) as month'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereYear('order_date', $currentYear)
            ->groupBy(DB::raw('MONTH(order_date)'))
            ->orderBy(DB::raw('MONTH(order_date)'))
            ->get()
            ->keyBy('month');

        // Top 5 bestselling products
        $bestsellers = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(line_total) as total_revenue')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Top 5 most frequent customers
        $frequentCustomers = Order::select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent')
            )
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        // ── Inventory Health — APPROVED adjustments only ───────
        $approvedAdjustmentsBase = StockAdjustment::where('status', 'approved')
            ->whereYear('adjustment_date', $currentYear);

        $totalAdjustedUnits = (clone $approvedAdjustmentsBase)
            ->sum(DB::raw('ABS(quantity)'));

        $totalAdjustmentTransactions = (clone $approvedAdjustmentsBase)
            ->count();

        // Breakdown by reason — approved only
        $adjustmentsByReason = (clone $approvedAdjustmentsBase)
            ->select(
                'reason',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(ABS(quantity)) as total_units')
            )
            ->groupBy('reason')
            ->get()
            ->keyBy('reason');

        // Returns — placeholders if ReturnTransaction model not yet present
        $totalReturnedUnits       = 0;
        $totalReturnTransactions  = 0;
        $totalRefunded            = 0;
        $recentReturns            = collect();

        // Uncomment once the returns feature is fully wired up:
        // $totalReturnedUnits      = ReturnTransaction::whereYear('return_date', $currentYear)->sum('quantity');
        // $totalReturnTransactions = ReturnTransaction::whereYear('return_date', $currentYear)->count();
        // $totalRefunded           = ReturnTransaction::whereYear('return_date', $currentYear)->sum('refund_amount');
        // $recentReturns           = ReturnTransaction::with(['order', 'product'])
        //     ->whereYear('return_date', $currentYear)
        //     ->latest('return_date')->limit(5)->get();

        return view('admin.reports.index', compact(
            'currentYear',
            'currentMonth',
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'monthlySales',
            'bestsellers',
            'frequentCustomers',
            'totalAdjustedUnits',
            'totalAdjustmentTransactions',
            'adjustmentsByReason',
            'totalReturnedUnits',
            'totalReturnTransactions',
            'totalRefunded',
            'recentReturns',
        ));
    }

    // ── PDF: Monthly Sales Report ───────────────────────────────
    public function exportMonthlySales(Request $request)
    {
        $year  = (int) $request->input('year',  now()->year);
        $month = (int) $request->input('month', now()->month);

        $orders = Order::with(['customer', 'deliveryMethod', 'orderLines.product'])
            ->whereYear('order_date', $year)
            ->whereMonth('order_date', $month)
            ->orderBy('order_date')
            ->get();

        $totalRevenue  = $orders->sum('total');
        $totalOrders   = $orders->count();
        $totalDelivery = $orders->sum('delivery_fee');
        $totalSubtotal = $orders->sum('subtotal');
        $byStatus      = $orders->groupBy('payment_status')->map->count();
        $monthName     = now()->setDate($year, $month, 1)->format('F Y');

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
        $year = (int) $request->input('year', now()->year);

        $products = OrderLine::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_price')
            )
            ->with('product.supplier')
            ->whereHas('order', fn ($q) => $q->whereYear('order_date', $year))
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
        $year = (int) $request->input('year', now()->year);

        $customers = Order::select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent'),
                DB::raw('AVG(total) as avg_order_value'),
                DB::raw('MAX(order_date) as last_order_date')
            )
            ->with('customer')
            ->whereYear('order_date', $year)
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
        $year = (int) $request->input('year', now()->year);

        $monthlySales = Order::select(
                DB::raw('MONTH(order_date) as month'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(delivery_fee) as delivery_fee'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereYear('order_date', $year)
            ->groupBy(DB::raw('MONTH(order_date)'))
            ->orderBy(DB::raw('MONTH(order_date)'))
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
            ->whereHas('order', fn ($q) => $q->whereYear('order_date', $year))
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
            ->whereYear('order_date', $year)
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