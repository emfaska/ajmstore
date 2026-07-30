<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'trashed']);
        $brands = $this->brandService->advancedSearchPaginated($filters, 10);
        
        return view('brands.index', compact('brands', 'filters'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        
        $this->brandService->createBrand($data);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    public function show($id)
    {
        $brand = $this->brandService->getBrandById($id);
        return view('brands.show', compact('brand'));
    }

    public function edit($id)
    {
        $brand = $this->brandService->getBrandById($id);
        return view('brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->brandService->updateBrand($id, $data);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->brandService->deleteBrand($id);
        return redirect()->route('brands.index')->with('success', 'Brand berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->brandService->restoreBrand($id);
        return redirect()->route('brands.index', ['trashed' => 1])->with('success', 'Brand berhasil dipulihkan.');
    }
}
