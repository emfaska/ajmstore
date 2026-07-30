<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CashTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    protected SaleService $saleService;
    protected ProductService $productService;
    protected CashTransactionService $cashTransactionService;

    public function __construct(
        SaleService $saleService,
        ProductService $productService,
        CashTransactionService $cashTransactionService
    ) {
        $this->saleService = $saleService;
        $this->productService = $productService;
        $this->cashTransactionService = $cashTransactionService;
    }

    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'payment_status', 'start_date', 'end_date', 'trashed']);
        
        $query = Sale::query()->with(['customer', 'paymentMethod']);

        // Check for trashed items if requested
        if (isset($filters['trashed']) && $filters['trashed'] == '1') {
            $query->onlyTrashed();
        }

        if (!empty($filters['search'])) {
            $query->where('invoice_number', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('sale_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('sale_date', '<=', $filters['end_date']);
        }

        $sales = $query->latest('id')->paginate(10);

        return view('sales.index', compact('sales', 'filters'));
    }

    /**
     * Show the POS cashier form.
     */
    public function create()
    {
        // Seeding default payment methods if they are empty
        if (PaymentMethod::count() === 0) {
            PaymentMethod::create(['name' => 'Tunai', 'description' => 'Pembayaran Tunai']);
            PaymentMethod::create(['name' => 'Transfer', 'description' => 'Pembayaran Transfer Bank']);
            PaymentMethod::create(['name' => 'QRIS', 'description' => 'Pembayaran QRIS']);
        }

        $customers = Customer::orderBy('name')->get();
        $vehicles = Vehicle::with('customer')->orderBy('license_plate')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        // Generate Unique Invoice Number: INV-YYYYMMDD-XXXX
        $today = date('Ymd');
        $count = Sale::withTrashed()->where('invoice_number', 'like', "INV-{$today}-%")->count() + 1;
        $invoiceNumber = 'INV-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Fetch products in stock
        $products = Product::whereNull('deleted_at')->orderBy('name')->get();

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
        $sale = Sale::withTrashed()->with(['customer', 'vehicle', 'paymentMethod', 'items.product'])->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    /**
     * Print the receipt for the specified sale.
     */
    public function print($id, Request $request)
    {
        $sale = Sale::withTrashed()->with(['customer', 'vehicle', 'paymentMethod', 'items.product'])->findOrFail($id);
        
        $cashReceived = $request->query('cash_received', 0);
        $change = $request->query('change', 0);

        return view('sales.print', compact('sale', 'cashReceived', 'change'));
    }

    /**
     * Soft delete the specified sale.
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Transaksi Penjualan berhasil dihapus (soft delete).');
    }

    /**
     * Restore the specified soft-deleted sale.
     */
    public function restore($id)
    {
        $sale = Sale::onlyTrashed()->findOrFail($id);
        $sale->restore();

        return redirect()->route('sales.index', ['trashed' => 1])->with('success', 'Transaksi Penjualan berhasil dipulihkan.');
    }

    /**
     * Search products in real-time.
     */
    public function searchProducts(Request $request)
    {
        $search = $request->query('query', '');
        
        $products = Product::whereNull('deleted_at')
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
