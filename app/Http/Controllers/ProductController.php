<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'brand_id', 'stock_status', 'trashed']);
        $products = $this->productService->advancedSearchPaginated($filters, 10);
        
        $categories = $this->productService->getAllCategories();
        $brands = $this->productService->getAllBrands();

        return view('products.index', compact('products', 'categories', 'brands', 'filters'));
    }

    public function create()
    {
        $categories = $this->productService->getAllCategories();
        $brands = $this->productService->getAllBrands();
        
        return view('products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image'] = $this->productService->handleImageUpload($request->file('image'));
        }

        $this->productService->createProduct($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id, ['category', 'brand']);
        return view('products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $categories = $this->productService->getAllCategories();
        $brands = $this->productService->getAllBrands();
        
        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $product = $this->productService->getProductById($id);
            if ($product->image) {
                $this->productService->deleteImage($product->image);
            }
            $data['image'] = $this->productService->handleImageUpload($request->file('image'));
        }

        $this->productService->updateProduct($id, $data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $this->productService->restoreProduct($id);
        return redirect()->route('products.index', ['trashed' => 1])->with('success', 'Produk berhasil dipulihkan.');
    }
}
