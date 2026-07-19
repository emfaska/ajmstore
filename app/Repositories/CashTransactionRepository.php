<?php

namespace App\Repositories;

use App\Models\CashTransaction;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CashTransactionRepository extends BaseRepository implements CashTransactionRepositoryInterface
{
    public function __construct(CashTransaction $model)
    {
        parent::__construct($model);
    }

    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('paymentMethod')
            ->where('type', $type)
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('paymentMethod')
            ->whereBetween('transaction_date', [$from, $to])
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    public function sumDebit(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->where('type', 'debit')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');
    }

    public function sumCredit(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->where('type', 'credit')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');
    }

    public function getByReferenceable(string $type, int $id, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('paymentMethod')
            ->where('referenceable_type', $type)
            ->where('referenceable_id', $id)
            ->latest()
            ->paginate($perPage);
    }
}
