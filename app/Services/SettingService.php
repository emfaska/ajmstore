<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingService extends BaseService
{
    /**
     * @var SettingRepositoryInterface
     */
    protected SettingRepositoryInterface $settingRepository;

    /**
     * Cache prefix string.
     */
    protected const CACHE_PREFIX = 'settings.';

    /**
     * SettingService constructor.
     *
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Membaca pengaturan berdasarkan key. Jika tidak ada di cache, ambil dari database lalu cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = $this->getCacheKey($key);

        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            return $this->settingRepository->getValue($key, $default);
        });
    }

    /**
     * Membaca beberapa pengaturan sekaligus dengan memanfaatkan cache.
     *
     * @param array $keys
     * @return array
     */
    public function getMany(array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        
        return $results;
    }

    /**
     * Menyimpan (create) atau mengubah (update) pengaturan berdasarkan key,
     * serta otomatis menghapus cache-nya agar data tetap sinkron.
     *
     * @param string $key
     * @param mixed $value
     * @return Model
     */
    public function set(string $key, mixed $value): Model
    {
        // Menyimpan atau memperbarui value di database
        $setting = $this->settingRepository->setValue($key, $value);

        // Menghapus cache saat data berubah
        $this->clearCache($key);

        return $setting;
    }

    /**
     * Menghapus cache untuk sebuah key pengaturan tertentu.
     *
     * @param string $key
     * @return void
     */
    public function clearCache(string $key): void
    {
        Cache::forget($this->getCacheKey($key));
    }

    /**
     * Mengambil struktur key cache.
     *
     * @param string $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }
}
