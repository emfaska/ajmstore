<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search suppliers by name, phone, or email.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;
}
