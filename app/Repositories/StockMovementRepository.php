<?php

namespace App\Repositories;

use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }

    public function getByProduct(int $productId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('product')
            ->where('product_id', $productId)
            ->latest()
            ->paginate($perPage);
    }

    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('product')
            ->where('type', $type)
            ->latest()
            ->paginate($perPage);
    }

    public function getByReferenceable(string $type, int $id): Collection
    {
        return $this->model->newQuery()
            ->with('product')
            ->where('referenceable_type', $type)
            ->where('referenceable_id', $id)
            ->get();
    }
}
