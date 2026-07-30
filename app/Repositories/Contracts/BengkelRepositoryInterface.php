<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BengkelRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search bengkels by name, pic, or phone.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Advanced search with filters (search, is_active, trashed).
     */
    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active bengkels list.
     */
    public function getActive(): Collection;

    /**
     * Restore soft deleted record.
     */
    public function restore(int|string $id): bool;
}
