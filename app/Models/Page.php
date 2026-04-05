<?php

namespace App\Models;

use App\Support\SitemapCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'in_menu',
        'sort',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $page): void {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::saved(function (): void {
            cache()->forget('menu_pages');
            SitemapCache::forget();
        });

        static::deleted(function (): void {
            cache()->forget('menu_pages');
            SitemapCache::forget();
        });
    }
}
