<?php

namespace App\Repositories;

use App\Models\Income;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class IncomeRepository extends BaseRepository implements IncomeRepositoryInterface
{
    public function __construct(Income $model)
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
            ->whereBetween('income_date', [$from, $to])
            ->latest('income_date')
            ->paginate($perPage);
    }

    public function sumTotal(string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->whereBetween('income_date', [$from, $to])
            ->sum('amount');
    }

    public function sumToday(): float
    {
        $today = Carbon::today()->toDateString();

        return $this->sumTotal($today, $today);
    }
}
