<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search customers by name, phone, or email.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return a customer with their vehicles eager-loaded.
     */
    public function findWithVehicles(int $id): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Return all vehicles belonging to a customer.
     */
    public function getVehicles(int $customerId): Collection;
}
