<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockMovementRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Return stock movements for a specific product.
     */
    public function getByProduct(int $productId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter movements by type (in | out | adjustment).
     */
    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return movements for a polymorphic parent (e.g. PurchaseItem, SaleItem).
     */
    public function getByReferenceable(string $type, int $id): Collection;
}
