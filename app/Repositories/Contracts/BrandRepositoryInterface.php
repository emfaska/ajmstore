<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search brands by name.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return only brands that have at least one product.
     */
    public function withProducts(): Collection;

    /**
     * Advanced search with filters.
     */
    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Restore soft deleted brand.
     */
    public function restore(int|string $id): bool;
}
