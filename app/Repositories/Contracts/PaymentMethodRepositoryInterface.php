<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PaymentMethodRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Return all payment methods (no soft-delete; usually small dataset).
     */
    public function getAll(): Collection;

    /**
     * Search payment methods by name.
     */
    public function search(string $keyword): Collection;
}
