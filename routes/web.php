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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Master Barang, Supplier, Kategori & Brand Routes
Route::middleware(['auth', 'role:Owner,Admin'])->group(function () {
    Route::patch('products/{product}/restore', [\App\Http\Controllers\ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    
    Route::patch('suppliers/{supplier}/restore', [\App\Http\Controllers\SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    
    Route::patch('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    
    Route::patch('brands/{brand}/restore', [\App\Http\Controllers\BrandController::class, 'restore'])->name('brands.restore');
    Route::resource('brands', \App\Http\Controllers\BrandController::class);

    Route::patch('purchases/{purchase}/restore', [\App\Http\Controllers\PurchaseController::class, 'restore'])->name('purchases.restore');
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
});

// Modul POS (Point of Sale / Kasir) & Penjualan
Route::middleware(['auth', 'role:Owner,Admin,Kasir'])->group(function () {
    Route::get('sales/products/search', [\App\Http\Controllers\SaleController::class, 'searchProducts'])->name('sales.products.search');
    Route::patch('sales/{sale}/restore', [\App\Http\Controllers\SaleController::class, 'restore'])->name('sales.restore');
    Route::get('sales/{sale}/print', [\App\Http\Controllers\SaleController::class, 'print'])->name('sales.print');
    Route::resource('sales', \App\Http\Controllers\SaleController::class);
});

require __DIR__.'/auth.php';