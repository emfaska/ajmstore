<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The Eloquent model managed by this repository.
     */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    public function all(array $with = []): Collection
    {
        return $this->model->newQuery()->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->model->newQuery()->with($with)->latest()->paginate($perPage);
    }

    public function find(int|string $id, array $with = []): ?Model
    {
        return $this->model->newQuery()->with($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = []): Model
    {
        return $this->model->newQuery()->with($with)->findOrFail($id);
    }

    public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        return $this->model->newQuery()->with($with)->where($column, $value)->first();
    }

    public function count(): int
    {
        return $this->model->newQuery()->count();
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int|string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);

        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }
}
