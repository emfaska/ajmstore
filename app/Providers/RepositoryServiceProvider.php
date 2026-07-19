<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Contracts
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;

// Implementations
use App\Repositories\CategoryRepository;
use App\Repositories\BrandRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\PurchaseRepository;
use App\Repositories\SaleRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\IncomeRepository;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\CashTransactionRepository;
use App\Repositories\StockMovementRepository;
use App\Repositories\SettingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All interface => implementation bindings.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        CategoryRepositoryInterface::class      => CategoryRepository::class,
        BrandRepositoryInterface::class         => BrandRepository::class,
        ProductRepositoryInterface::class       => ProductRepository::class,
        SupplierRepositoryInterface::class      => SupplierRepository::class,
        CustomerRepositoryInterface::class      => CustomerRepository::class,
        VehicleRepositoryInterface::class       => VehicleRepository::class,
        PurchaseRepositoryInterface::class      => PurchaseRepository::class,
        SaleRepositoryInterface::class          => SaleRepository::class,
        ExpenseRepositoryInterface::class       => ExpenseRepository::class,
        IncomeRepositoryInterface::class        => IncomeRepository::class,
        PaymentMethodRepositoryInterface::class => PaymentMethodRepository::class,
        CashTransactionRepositoryInterface::class => CashTransactionRepository::class,
        StockMovementRepositoryInterface::class => StockMovementRepository::class,
        SettingRepositoryInterface::class       => SettingRepository::class,
    ];

    public function register(): void
    {
        // Bindings are automatically resolved via the $bindings property above.
    }

    public function boot(): void
    {
        //
    }
}
