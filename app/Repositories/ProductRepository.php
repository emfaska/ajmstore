<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['category', 'brand'])
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getLowStock(): Collection
    {
        return $this->model->newQuery()
            ->with(['category', 'brand'])
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();
    }

    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['category', 'brand'])
            ->where('category_id', $categoryId)
            ->latest()
            ->paginate($perPage);
    }

    public function getByBrand(int $brandId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['category', 'brand'])
            ->where('brand_id', $brandId)
            ->latest()
            ->paginate($perPage);
    }

    public function findBySku(string $sku): ?Model
    {
        return $this->model->newQuery()->where('sku', $sku)->first();
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }
}
