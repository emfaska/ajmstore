<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ExpenseRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search expenses by title.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter expenses within a date range (Y-m-d).
     */
    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return the total expense amount for a given date range.
     */
    public function sumTotal(string $from, string $to): float;

    /**
     * Return the total expense amount for today.
     */
    public function sumToday(): float;
}
