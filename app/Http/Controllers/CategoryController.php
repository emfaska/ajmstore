<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'trashed']);
        $categories = $this->categoryService->advancedSearchPaginated($filters, 10);
        
        return view('categories.index', compact('categories', 'filters'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        
        $this->categoryService->createCategory($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $this->categoryService->updateCategory($id, $data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->categoryService->restoreCategory($id);
        return redirect()->route('categories.index', ['trashed' => 1])->with('success', 'Kategori berhasil dipulihkan.');
    }
}
