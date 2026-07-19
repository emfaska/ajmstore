<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CashTransactionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Filter transactions by type (debit | credit).
     */
    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter transactions within a date range (Y-m-d).
     */
    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return the total debit amount for a given date range.
     */
    public function sumDebit(string $from, string $to): float;

    /**
     * Return the total credit amount for a given date range.
     */
    public function sumCredit(string $from, string $to): float;

    /**
     * Return all transactions for a polymorphic parent (e.g. Sale, Purchase).
     */
    public function getByReferenceable(string $type, int $id, int $perPage = 15): LengthAwarePaginator;
}
