<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Return all records, optionally with eager-loaded relations.
     *
     * @param  string[]  $with
     */
    public function all(array $with = []): Collection;

    /**
     * Paginate records.
     *
     * @param  string[]  $with
     */
    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator;

    /**
     * Find a record by its primary key.
     *
     * @param  string[]  $with
     */
    public function find(int|string $id, array $with = []): ?Model;

    /**
     * Find a record by primary key or throw ModelNotFoundException.
     *
     * @param  string[]  $with
     */
    public function findOrFail(int|string $id, array $with = []): Model;

    /**
     * Find a record matching the given column => value pair.
     *
     * @param  string[]  $with
     */
    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * Update an existing record by primary key.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int|string $id, array $data): Model;

    /**
     * Delete a record by primary key (soft-delete if model uses SoftDeletes).
     */
    public function delete(int|string $id): bool;

    /**
     * Return a count of all records.
     */
    public function count(): int;
}
