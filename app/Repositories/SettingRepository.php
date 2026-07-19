<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        $setting = $this->model->newQuery()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public function setValue(string $key, mixed $value): Model
    {
        return $this->model->newQuery()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function getMany(array $keys): array
    {
        return $this->model->newQuery()
            ->whereIn('key', $keys)
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }
}
