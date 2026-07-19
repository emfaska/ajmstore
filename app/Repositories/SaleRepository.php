<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    public function __construct(Sale $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['customer', 'paymentMethod'])
            ->where('invoice_number', 'like', "%{$keyword}%")
            ->latest()
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['customer', 'paymentMethod'])
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    public function getByPaymentStatus(string $paymentStatus, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['customer', 'paymentMethod'])
            ->where('payment_status', $paymentStatus)
            ->latest()
            ->paginate($perPage);
    }

    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['customer', 'paymentMethod'])
            ->whereBetween('sale_date', [$from, $to])
            ->latest('sale_date')
            ->paginate($perPage);
    }

    public function sumTotal(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$from, $to])
            ->sum('total_amount');
    }

    public function sumToday(): float
    {
        $today = Carbon::today()->toDateString();

        return $this->sumTotal($today, $today);
    }

    public function findByInvoice(string $invoiceNumber): ?Model
    {
        return $this->model->newQuery()
            ->with(['customer', 'vehicle', 'paymentMethod', 'items.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }

    public function getByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['paymentMethod'])
            ->where('customer_id', $customerId)
            ->latest('sale_date')
            ->paginate($perPage);
    }
}
