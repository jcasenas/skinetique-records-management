<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Middleware\AuthenticateEmployee;
use App\Http\Middleware\OwnerOnly;
use Illuminate\Support\Facades\Route;

// ── Guest routes ─────────────────────────────────────────────
Route::middleware('guest:employee')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::get('/', fn () => redirect()->route('login'));

// ── Authenticated routes ──────────────────────────────────────
Route::middleware(AuthenticateEmployee::class)->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Orders
        Route::get('/orders',                  [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders',                 [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/archives',         [OrderController::class, 'archives'])->name('orders.archives');
        Route::get('/orders/{order}',          [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/archive', [OrderController::class, 'archive'])->name('orders.archive');
        Route::post('/orders/{order}/returns', [ReturnController::class, 'store'])->name('orders.returns.store');

        // Products
        Route::get('/products',              [ProductController::class, 'index'])->name('products.index');
        Route::post('/products',             [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}',    [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Stocks
        Route::get('/stocks',               [StockInController::class, 'index'])->name('stocks.index');
        Route::post('/stocks',              [StockInController::class, 'store'])->name('stocks.store');
        Route::post('/stocks/adjustments',  [StockAdjustmentController::class, 'store'])->name('stocks.adjustments.store');

        // Customers
        Route::get('/customers',               [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers',              [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}',    [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // Payments
        Route::get('/payments',                   [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/history',           [PaymentController::class, 'history'])->name('payments.history');
        Route::post('/payments',                  [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

        // Reports
        Route::get('/reports',                           [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/monthly-sales',      [ReportController::class, 'exportMonthlySales'])->name('reports.export.monthly-sales');
        Route::get('/reports/export/bestsellers',        [ReportController::class, 'exportBestsellers'])->name('reports.export.bestsellers');
        Route::get('/reports/export/frequent-customers', [ReportController::class, 'exportFrequentCustomers'])->name('reports.export.frequent-customers');
        Route::get('/reports/export/annual-summary',     [ReportController::class, 'exportAnnualSummary'])->name('reports.export.annual-summary');

        // Help
        Route::get('/help', [HelpController::class, 'index'])->name('help.index');

        // Settings
        Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // ── Owner-only routes ─────────────────────────────────
        Route::middleware(OwnerOnly::class)->group(function () {
            Route::get('/employees',               [EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/employees',              [EmployeeController::class, 'store'])->name('employees.store');
            Route::put('/employees/{employee}',    [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        });

    });
});