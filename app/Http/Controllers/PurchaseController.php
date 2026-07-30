<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Services\PurchaseService;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;
    protected SupplierRepositoryInterface $supplierRepository;
    protected ProductRepositoryInterface $productRepository;
    protected PaymentMethodRepositoryInterface $paymentMethodRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;

    public function __construct(
        PurchaseService $purchaseService,
        SupplierRepositoryInterface $supplierRepository,
        ProductRepositoryInterface $productRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PurchaseRepositoryInterface $purchaseRepository
    ) {
        $this->purchaseService = $purchaseService;
        $this->supplierRepository = $supplierRepository;
        $this->productRepository = $productRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'supplier_id', 'status', 'payment_status', 'start_date', 'end_date', 'trashed']);
        $purchases = $this->purchaseService->advancedSearchPaginated($filters, 10);
        $suppliers = $this->supplierRepository->all()->sortBy('name');

        return view('purchases.index', compact('purchases', 'filters', 'suppliers'));
    }

    public function create()
    {
        $suppliers = $this->supplierRepository->all()->where('is_active', true)->sortBy('name');
        $products = $this->productRepository->all()->sortBy('name');
        $paymentMethods = $this->paymentMethodRepository->all()->sortBy('name');

        // Generate Invoice Number
        $count = $this->purchaseRepository->countWithTrashed() + 1;
        $invoiceNumber = 'INV-PRC-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'paymentMethods', 'invoiceNumber'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $data = $request->validated();
        
        $this->purchaseService->createPurchase($data, $request->input('items', []));

        return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian berhasil disimpan.');
    }

    public function show($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id, ['supplier', 'items.product', 'cashTransactions.paymentMethod']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id, ['supplier', 'items.product']);

        // Hanya boleh diedit jika status masih Draft (pending) dan belum lunas (paid)
        if ($purchase->status === 'completed' || $purchase->payment_status === 'paid') {
            return redirect()->route('purchases.index')->with('error', 'Hanya transaksi dengan status pending dan belum lunas yang dapat diubah.');
        }

        $suppliers = $this->supplierRepository->all()->sortBy('name');
        $products = $this->productRepository->all()->sortBy('name');
        $paymentMethods = $this->paymentMethodRepository->all()->sortBy('name');

        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'paymentMethods'));
    }

    public function update(UpdatePurchaseRequest $request, $id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);

        if ($purchase->status === 'completed' || $purchase->payment_status === 'paid') {
            return redirect()->route('purchases.index')->with('error', 'Transaksi yang telah selesai atau lunas tidak dapat diubah.');
        }

        $data = $request->validated();
        $this->purchaseService->updatePurchase($id, $data, $request->input('items', []));

        return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->purchaseService->deletePurchase($id);
        return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->purchaseService->restorePurchase($id);
        return redirect()->route('purchases.index', ['trashed' => 1])->with('success', 'Transaksi Pembelian berhasil dipulihkan.');
    }
}
