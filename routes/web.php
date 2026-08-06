<?php

use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Programmatic DB Setup to bypass CLI restriction
Route::get('/admin/setup-db', function () {
    try {
        // Delete old temporary migration files
        $oldMigrations = [
            database_path('migrations/2026_07_17_000001_create_products_table.php'),
            database_path('migrations/2026_07_17_000002_create_orders_table.php'),
            database_path('migrations/2026_07_17_000003_create_order_items_table.php'),
        ];

        foreach ($oldMigrations as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $config = config('database.connections.mysql');
        $pdo = new PDO(
            "mysql:host={$config['host']};port={$config['port']}",
            $config['username'],
            $config['password']
        );
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        Illuminate\Support\Facades\Artisan::call('migrate:fresh');
        $output = Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Database successfully created and migrated!',
            'output' => explode("\n", trim($output))
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database setup failed: ' . $e->getMessage()
        ], 500);
    }
});

// Analysis Resource Routes
Route::resource('analysis', AnalysisController::class)->only(['index']);

// Protected Dashboard Route
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:Owner,Admin'])
    ->name('dashboard');

// Master Barang, Supplier, Kategori & Brand Routes
Route::middleware(['auth', 'role:Owner,Admin'])->group(function () {
    Route::patch('products/{product}/restore', [\App\Http\Controllers\ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    Route::patch('suppliers/{supplier}/restore', [\App\Http\Controllers\SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    Route::patch('customers/{customer}/restore', [\App\Http\Controllers\CustomerController::class, 'restore'])->name('customers.restore');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    Route::patch('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    Route::patch('brands/{brand}/restore', [\App\Http\Controllers\BrandController::class, 'restore'])->name('brands.restore');
    Route::resource('brands', \App\Http\Controllers\BrandController::class);

    Route::patch('purchases/{purchase}/restore', [\App\Http\Controllers\PurchaseController::class, 'restore'])->name('purchases.restore');
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);

    // Laporan Kas
    Route::get('reports/cash', [\App\Http\Controllers\CashReportController::class, 'index'])->name('reports.cash');
    Route::get('reports/cash/pdf', [\App\Http\Controllers\CashReportController::class, 'exportPdf'])->name('reports.cash.pdf');

    // Laporan Penjualan
    Route::get('reports/sales', [\App\Http\Controllers\SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('reports/sales/pdf', [\App\Http\Controllers\SalesReportController::class, 'exportPdf'])->name('reports.sales.pdf');
    Route::get('reports/sales/excel', [\App\Http\Controllers\SalesReportController::class, 'exportExcel'])->name('reports.sales.excel');

    // Laporan Laba Rugi (Profit & Loss)
    Route::get('reports/profit-loss', [\App\Http\Controllers\ProfitLossController::class, 'index'])->name('reports.profit_loss');
    Route::get('reports/profit-loss/pdf', [\App\Http\Controllers\ProfitLossController::class, 'exportPdf'])->name('reports.profit_loss.pdf');

    // Laporan Pembelian
    Route::get('reports/purchases', [\App\Http\Controllers\PurchaseReportController::class, 'index'])->name('reports.purchases');
    Route::get('reports/purchases/pdf', [\App\Http\Controllers\PurchaseReportController::class, 'exportPdf'])->name('reports.purchases.pdf');
    Route::get('reports/purchases/excel', [\App\Http\Controllers\PurchaseReportController::class, 'exportExcel'])->name('reports.purchases.excel');

    // Pengaturan Toko
    Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

// Modul POS (Point of Sale / Kasir) & Penjualan
Route::middleware(['auth', 'role:Owner,Admin,Kasir'])->group(function () {
    Route::patch('sales/{sale}/restore', [\App\Http\Controllers\SaleController::class, 'restore'])->name('sales.restore');
    Route::get('sales/{sale}/print', [\App\Http\Controllers\SaleController::class, 'print'])->name('sales.print');
    Route::resource('sales', \App\Http\Controllers\SaleController::class);
});

require __DIR__ . '/auth.php';
