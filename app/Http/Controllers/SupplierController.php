<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'trashed']);
        $suppliers = $this->supplierService->advancedSearchPaginated($filters, 10);
        
        return view('suppliers.index', compact('suppliers', 'filters'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        
        $this->supplierService->createSupplier($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->supplierService->updateSupplier($id, $data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->supplierService->deleteSupplier($id);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->supplierService->restoreSupplier($id);
        return redirect()->route('suppliers.index', ['trashed' => 1])->with('success', 'Supplier berhasil dipulihkan.');
    }
}
