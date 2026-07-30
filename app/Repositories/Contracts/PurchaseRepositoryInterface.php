<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search purchases by invoice number.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter purchases by status (pending | completed | cancelled).
     */
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter purchases within a date range (Y-m-d).
     */
    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return the total purchase amount for a given date range.
     */
    public function sumTotal(string $from, string $to): float;

    /**
     * Advanced search with filters (search, supplier_id, status, start_date, end_date, etc.).
     */
    public function advancedSearch(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a purchase by invoice number.
     */
    public function findByInvoice(string $invoiceNumber): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Restore soft deleted purchase.
     */
    public function restore(int|string $id): bool;
}
