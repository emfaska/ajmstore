<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a setting value by key.
     */
    public function getValue(string $key, mixed $default = null): mixed;

    /**
     * Set (create or update) a setting value by key.
     */
    public function setValue(string $key, mixed $value): \Illuminate\Database\Eloquent\Model;

    /**
     * Return multiple settings as a key => value array.
     *
     * @param  string[]  $keys
     * @return array<string, mixed>
     */
    public function getMany(array $keys): array;
}
