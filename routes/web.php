<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report', [DashboardController::class, 'downloadReport'])->name('dashboard.report');
    Route::get('/dashboard/activity-log', [DashboardController::class, 'downloadActivityLog'])->name('dashboard.activity-log');

    // Customer & Brands
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/brands', [CustomerController::class, 'addBrand'])->name('customers.brands.store');
    Route::delete('customers/{customer}/brands/{brand}', [CustomerController::class, 'deleteBrand'])->name('customers.brands.destroy');

    // Products
    Route::resource('products', ProductController::class);
    // API endpoint for Smart Autofill (gets products for a selected customer)
    Route::get('api/customers/{customer}/products', [ProductController::class, 'getProductsByCustomer'])->name('api.customer.products');
    Route::get('api/customers/{customer}/invoices', [InvoiceController::class, 'getInvoicesByCustomer'])->name('api.customer.invoices');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Delivery Orders (Surat Jalan)
    Route::resource('delivery-orders', DeliveryOrderController::class);
    Route::get('delivery-orders/{delivery_order}/pdf', [DeliveryOrderController::class, 'downloadPdf'])->name('delivery-orders.pdf');
    Route::get('delivery-orders/{delivery_order}/print', [DeliveryOrderController::class, 'print'])->name('delivery-orders.print');

    // Admin Only Routes (Master Data, Onboarding, Settings)
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        // Onboarding Data
        Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
        Route::post('/onboarding/import', [OnboardingController::class, 'import'])->name('onboarding.import');
        Route::get('/onboarding/verify', [OnboardingController::class, 'verify'])->name('onboarding.verify');

        // Master Data Hub
        Route::get('/master-data', [DashboardController::class, 'masterData'])->name('master-data.index');

        // Settings
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/bank/{bank}', [SystemSettingController::class, 'deleteBank'])->name('settings.bank.destroy');

        // User Management (Manajemen Pengguna)
        Route::resource('users', UserController::class);
    });

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
