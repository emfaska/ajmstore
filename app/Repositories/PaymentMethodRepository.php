<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    public function __construct(PaymentMethod $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get();
    }

    public function search(string $keyword): Collection
    {
        return $this->model->newQuery()
            ->where('name', 'like', "%{$keyword}%")
            ->orderBy('name')
            ->get();
    }
}
