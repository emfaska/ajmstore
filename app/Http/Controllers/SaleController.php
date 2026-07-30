<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CashTransactionService;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected SaleService $saleService;
    protected ProductService $productService;
    protected CashTransactionService $cashTransactionService;
    protected CustomerRepositoryInterface $customerRepository;
    protected VehicleRepositoryInterface $vehicleRepository;
    protected PaymentMethodRepositoryInterface $paymentMethodRepository;
    protected ProductRepositoryInterface $productRepository;
    protected SaleRepositoryInterface $saleRepository;

    public function __construct(
        SaleService $saleService,
        ProductService $productService,
        CashTransactionService $cashTransactionService,
        CustomerRepositoryInterface $customerRepository,
        VehicleRepositoryInterface $vehicleRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ProductRepositoryInterface $productRepository,
        SaleRepositoryInterface $saleRepository
    ) {
        $this->saleService = $saleService;
        $this->productService = $productService;
        $this->cashTransactionService = $cashTransactionService;
        $this->customerRepository = $customerRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->productRepository = $productRepository;
        $this->saleRepository = $saleRepository;
    }

    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'payment_status', 'start_date', 'end_date', 'trashed']);
        $sales = $this->saleRepository->advancedSearch($filters, 10);

        return view('sales.index', compact('sales', 'filters'));
    }

    /**
     * Show the POS cashier form.
     */
    public function create()
    {
        // Seeding default payment methods if they are empty
        if ($this->paymentMethodRepository->count() === 0) {
            $this->paymentMethodRepository->create(['name' => 'Tunai', 'description' => 'Pembayaran Tunai']);
            $this->paymentMethodRepository->create(['name' => 'Transfer', 'description' => 'Pembayaran Transfer Bank']);
            $this->paymentMethodRepository->create(['name' => 'QRIS', 'description' => 'Pembayaran QRIS']);
        }

        $customers = $this->customerRepository->all()->sortBy('name');
        $vehicles = $this->vehicleRepository->all(['customer'])->sortBy('license_plate');
        $paymentMethods = $this->paymentMethodRepository->all()->sortBy('name');

        // Generate Unique Invoice Number: INV-YYYYMMDD-XXXX
        $today = date('Ymd');
        $count = $this->saleRepository->countTodayWithInvoicePattern("INV-{$today}-%") + 1;
        $invoiceNumber = 'INV-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Fetch products in stock
        $products = $this->productRepository->all()->whereNull('deleted_at')->sortBy('name');

        return view('sales.create', compact('customers', 'vehicles', 'paymentMethods', 'invoiceNumber', 'products'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $data = $request->validated();

        // Save transaction using SaleService
        $sale = $this->saleService->createSale($data, $request->input('items', []));

        $cashReceived = $request->input('cash_received', 0);
        $change = $request->input('change', 0);

        return redirect()->route('sales.show', $sale->id)
            ->with('success', 'Transaksi Penjualan berhasil disimpan.')
            ->with('cash_received', $cashReceived)
            ->with('change', $change);
    }

    /**
     * Display the specified sale details.
     */
    public function show($id)
    {
        $sale = $this->saleRepository->findWithTrashedOrFail($id, ['customer', 'vehicle', 'paymentMethod', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Print the receipt for the specified sale.
     */
    public function print($id, Request $request)
    {
        $sale = $this->saleRepository->findWithTrashedOrFail($id, ['customer', 'vehicle', 'paymentMethod', 'items.product']);
        
        $cashReceived = $request->query('cash_received', 0);
        $change = $request->query('change', 0);

        return view('sales.print', compact('sale', 'cashReceived', 'change'));
    }

    /**
     * Soft delete the specified sale.
     */
    public function destroy($id)
    {
        $sale = $this->saleRepository->findOrFail($id);
        $this->saleRepository->delete($sale->id);

        return redirect()->route('sales.index')->with('success', 'Transaksi Penjualan berhasil dihapus (soft delete).');
    }

    /**
     * Restore the specified soft-deleted sale.
     */
    public function restore($id)
    {
        $this->saleRepository->restore($id);

        return redirect()->route('sales.index', ['trashed' => 1])->with('success', 'Transaksi Penjualan berhasil dipulihkan.');
    }
}
