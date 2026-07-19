<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface IncomeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search incomes by title.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter incomes within a date range (Y-m-d).
     */
    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return the total income amount for a given date range.
     */
    public function sumTotal(string $from, string $to): float;

    /**
     * Return the total income amount for today.
     */
    public function sumToday(): float;
}
