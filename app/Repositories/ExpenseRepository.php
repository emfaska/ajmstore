<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }

    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('title', 'like', "%{$keyword}%")
            ->latest()
            ->paginate($perPage);
    }

    public function getByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->whereBetween('expense_date', [$from, $to])
            ->latest('expense_date')
            ->paginate($perPage);
    }

    public function sumTotal(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');
    }

    public function sumToday(): float
    {
        $today = Carbon::today()->toDateString();

        return $this->sumTotal($today, $today);
    }
}
