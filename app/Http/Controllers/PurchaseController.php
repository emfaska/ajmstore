<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Services\PurchaseService;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'supplier_id', 'status', 'payment_status', 'start_date', 'end_date', 'trashed']);
        $purchases = $this->purchaseService->advancedSearchPaginated($filters, 10);
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'filters', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        // Generate Invoice Number
        $count = Purchase::withTrashed()->count() + 1;
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

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

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
