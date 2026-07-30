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

    public function findByBarcode(string $barcode): ?Model
    {
        return $this->model->newQuery()->where('barcode', $barcode)->first();
    }

    public function restore(int|string $id): bool
    {
        $record = $this->model->withTrashed()->findOrFail($id);
        return (bool) $record->restore();
    }

    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['category', 'brand']);

        // Check for trashed items if requested
        if (isset($filters['trashed']) && $filters['trashed'] == '1') {
            $query->onlyTrashed();
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%")
                  ->orWhere('barcode', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock');
            } elseif ($filters['stock_status'] === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($filters['stock_status'] === 'in') {
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        return $query->latest()->paginate($perPage);
    }
}
