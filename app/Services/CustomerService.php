<?php

namespace App\Services;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService extends BaseService
{
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers(array $with = []): Collection
    {
        return $this->customerRepository->all($with);
    }

    public function getCustomersPaginated(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage, $with);
    }

    public function advancedSearchPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->advancedSearch($filters, $perPage);
    }

    public function getCustomerById(int|string $id, array $with = []): Model
    {
        return $this->customerRepository->findOrFail($id, $with);
    }

    public function createCustomer(array $data): Model
    {
        return $this->customerRepository->create($data);
    }

    public function updateCustomer(int|string $id, array $data): Model
    {
        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer(int|string $id): bool
    {
        return $this->customerRepository->delete($id);
    }

    public function restoreCustomer(int|string $id): bool
    {
        return $this->customerRepository->restore($id);
    }

    public function findCustomerWithVehicles(int|string $id): ?Model
    {
        return $this->customerRepository->findWithVehicles($id);
    }

    public function getCustomerVehicles(int|string $id)
    {
        return $this->customerRepository->getVehicles($id);
    }
}
