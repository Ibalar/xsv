<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected const CACHE_KEY_PREFIX = 'site_setting_';

    protected static array $requestCache = [];

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => AsArrayObject::class,
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value, ?string $description = null): self
    {
        $data = ['value' => $value];

        if ($description !== null) {
            $data['description'] = $description;
        }

        return static::updateOrCreate(['key' => $key], $data);
    }

    public static function forget(string $key): bool
    {
        $deleted = static::where('key', $key)->delete();

        if ($deleted) {
            static::flushCacheByKey($key);
        }

        return $deleted > 0;
    }

    public static function getCached(string $key, mixed $default = null): mixed
    {
        if (isset(static::$requestCache[$key])) {
            return static::$requestCache[$key];
        }

        $cacheKey = static::CACHE_KEY_PREFIX . $key;

        return static::$requestCache[$key] = Cache::rememberForever($cacheKey, function () use ($key, $default) {
            return static::get($key, $default);
        });
    }

    public static function flushCache(): void
    {
        $keys = static::pluck('key');

        foreach ($keys as $key) {
            static::flushCacheByKey($key);
        }
    }

    protected static function flushCacheByKey(string $key): void
    {
        unset(static::$requestCache[$key]);
        Cache::forget(static::CACHE_KEY_PREFIX . $key);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (self $setting) {
            static::flushCacheByKey($setting->key);
        });

        static::deleted(function (self $setting) {
            static::flushCacheByKey($setting->key);
        });
    }

    public static function contacts(): array
    {
        $data = static::normalizeArray(static::getCached('contacts', []));

        $phones = [];

        if (isset($data['value']) && \is_array($data['value'])) {
            $phones = $data['value'];
        } elseif ($data !== []) {
            foreach ($data as $key => $value) {
                if (is_numeric($key) && \is_array($value) && isset($value['number'])) {
                    $phones[] = $value;
                }
            }
        }

        return [
            'phones' => $data['phones'] ?? $phones,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'working_hours' => $data['working_hours'] ?? null,
            'social_networks' => $data['social_networks'] ?? [],
        ];
    }

    public static function normalizeArray(mixed $value): array
    {
        if ($value instanceof \ArrayObject) {
            return $value->getArrayCopy();
        }

        if (\is_array($value)) {
            return $value;
        }

        if ($value instanceof \Traversable) {
            return iterator_to_array($value);
        }

        return [];
    }
}
