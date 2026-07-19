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
}
