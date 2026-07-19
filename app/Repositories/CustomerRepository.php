<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findWithVehicles(int $id): ?Model
    {
        return $this->model->newQuery()
            ->with('vehicles')
            ->find($id);
    }

    public function getVehicles(int $customerId): Collection
    {
        $customer = $this->findOrFail($customerId);

        return $customer->vehicles()->get();
    }
}
