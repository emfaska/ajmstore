<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('name', 'like', "%{$keyword}%")
            ->latest()
            ->paginate($perPage);
    }

    public function withProducts(): Collection
    {
        return $this->model->newQuery()
            ->has('products')
            ->with('products')
            ->get();
    }
}
