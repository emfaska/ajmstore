<?php

namespace App\Services;

use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService extends BaseService
{
    protected BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAllBrands(array $with = []): Collection
    {
        return $this->brandRepository->all($with);
    }

    public function getBrandsPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->brandRepository->paginate($perPage, $with);
    }

    public function advancedSearchPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->brandRepository->advancedSearch($filters, $perPage);
    }

    public function getBrandById(int|string $id, array $with = []): Model
    {
        return $this->brandRepository->findOrFail($id, $with);
    }

    public function createBrand(array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        return $this->brandRepository->create($data);
    }

    public function updateBrand(int|string $id, array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        return $this->brandRepository->update($id, $data);
    }

    public function deleteBrand(int|string $id): bool
    {
        return $this->brandRepository->delete($id);
    }

    public function restoreBrand(int|string $id): bool
    {
        return $this->brandRepository->restore($id);
    }
}
