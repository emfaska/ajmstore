<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search vehicles by license_plate, brand, or model.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return vehicles belonging to a specific customer.
     */
    public function getByCustomer(int $customerId): Collection;

    /**
     * Find a vehicle by its license plate.
     */
    public function findByLicensePlate(string $plate): ?\Illuminate\Database\Eloquent\Model;
}
