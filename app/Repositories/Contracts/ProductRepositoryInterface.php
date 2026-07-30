<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search products by name or SKU.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Advanced search with filters.
     */
    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator;


    /**
     * Return products where stock <= min_stock.
     */
    public function getLowStock(): Collection;

    /**
     * Return products filtered by category.
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return products filtered by brand.
     */
    public function getByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a product by its SKU.
     */
    public function findBySku(string $sku): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Find a product by its slug.
     */
    public function findBySlug(string $slug): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Find a product by its barcode.
     */
    public function findByBarcode(string $barcode): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Restore a soft-deleted product.
     */
    public function restore(int|string $id): bool;
}
