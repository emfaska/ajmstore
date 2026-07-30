<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService extends BaseService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(array $with = []): Collection
    {
        return $this->categoryRepository->all($with);
    }

    public function getCategoriesPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage, $with);
    }

    public function advancedSearchPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->advancedSearch($filters, $perPage);
    }

    public function getCategoryById(int|string $id, array $with = []): Model
    {
        return $this->categoryRepository->findOrFail($id, $with);
    }

    public function createCategory(array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        // Generate slug if not provided, assuming Str::slug is handled or we can do it here
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int|string $id, array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int|string $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function restoreCategory(int|string $id): bool
    {
        return $this->categoryRepository->restore($id);
    }
}
