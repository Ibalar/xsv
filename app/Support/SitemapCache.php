<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class SitemapCache
{
    public static function forget(): void
    {
        Cache::forget(config('sitemap.cache.key', 'sitemap.xml'));
    }
}
