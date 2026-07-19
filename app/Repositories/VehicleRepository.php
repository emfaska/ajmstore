<?php

namespace App\Repositories;

use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('customer')
            ->where(function ($q) use ($keyword) {
                $q->where('license_plate', 'like', "%{$keyword}%")
                  ->orWhere('brand', 'like', "%{$keyword}%")
                  ->orWhere('model', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->get();
    }

    public function findByLicensePlate(string $plate): ?Model
    {
        return $this->model->newQuery()
            ->where('license_plate', $plate)
            ->first();
    }
}
