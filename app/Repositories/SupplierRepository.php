<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('pic_name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['trashed']) && $filters['trashed'] == '1') {
            $query->onlyTrashed();
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('pic_name', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function restore(int|string $id): bool
    {
        $record = $this->model->withTrashed()->findOrFail($id);
        return (bool) $record->restore();
    }
}
