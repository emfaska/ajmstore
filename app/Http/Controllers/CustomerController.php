<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'trashed']);
        $customers = $this->customerService->advancedSearchPaginated($filters, 10);

        return view('customers.index', compact('customers', 'filters'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $this->customerService->createCustomer($data);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id, ['vehicles']);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $data = $request->validated();
        $this->customerService->updateCustomer($id, $data);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->customerService->restoreCustomer($id);
        return redirect()->route('customers.index', ['trashed' => 1])->with('success', 'Pelanggan berhasil dipulihkan.');
    }
}
