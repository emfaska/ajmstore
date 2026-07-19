<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Search sales by invoice number.
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter sales by status (pending | completed | cancelled).
     */
    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter sales by payment status (unpaid | partially_paid | paid).
     */
    public function getByPaymentStatus(string $paymentStatus, int $perPage = 15): LengthAwarePaginator;

    /**
     * Filter sales within a date range (Y-m-d).
     */
    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    /**
     * Return the total sales amount for a given date range.
     */
    public function sumTotal(string $from, string $to): float;

    /**
     * Return the total sales amount for today.
     */
    public function sumToday(): float;

    /**
     * Find a sale by invoice number.
     */
    public function findByInvoice(string $invoiceNumber): ?\Illuminate\Database\Eloquent\Model;

    /**
     * Return sales belonging to a specific customer.
     */
    public function getByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;
}
