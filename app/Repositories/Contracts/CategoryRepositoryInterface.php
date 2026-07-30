<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search categories by name.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return only categories that have at least one product.
     */
    public function withProducts(): Collection;

    /**
     * Advanced search with filters.
     */
    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Restore soft deleted category.
     */
    public function restore(int|string $id): bool;
}
