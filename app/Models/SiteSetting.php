<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected const CACHE_KEY_PREFIX = 'site_setting_';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
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
        $cacheKey = static::CACHE_KEY_PREFIX . $key;

        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            return static::get($key, $default);
        });
    }

    public static function flushCache(): void
    {
        $keys = static::pluck('key');

        foreach ($keys as $key) {
            Cache::forget(static::CACHE_KEY_PREFIX . $key);
        }
    }

    protected static function flushCacheByKey(string $key): void
    {
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
}
