<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRepository extends BaseRepository implements PurchaseRepositoryInterface
{
    public function __construct(Purchase $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('supplier')
            ->where('invoice_number', 'like', "%{$keyword}%")
            ->latest()
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('supplier')
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('supplier')
            ->whereBetween('purchase_date', [$from, $to])
            ->latest('purchase_date')
            ->paginate($perPage);
    }

    public function sumTotal(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->where('status', 'completed')
            ->whereBetween('purchase_date', [$from, $to])
            ->sum('total_amount');
    }

    public function findByInvoice(string $invoiceNumber): ?Model
    {
        return $this->model->newQuery()
            ->with(['supplier', 'items.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }
}
