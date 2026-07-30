<?php

namespace App\Services;

use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService extends BaseService
{
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getAllSuppliers(array $with = []): Collection
    {
        return $this->supplierRepository->all($with);
    }

    public function getSuppliersPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->supplierRepository->paginate($perPage, $with);
    }

    public function advancedSearchPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->supplierRepository->advancedSearch($filters, $perPage);
    }

    public function getSupplierById(int|string $id, array $with = []): Model
    {
        return $this->supplierRepository->findOrFail($id, $with);
    }

    public function createSupplier(array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->supplierRepository->create($data);
    }

    public function updateSupplier(int|string $id, array $data): Model
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->supplierRepository->update($id, $data);
    }

    public function deleteSupplier(int|string $id): bool
    {
        return $this->supplierRepository->delete($id);
    }

    public function restoreSupplier(int|string $id): bool
    {
        return $this->supplierRepository->restore($id);
    }
}
