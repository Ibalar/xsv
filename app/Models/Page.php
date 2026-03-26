<?php

namespace App\Models;

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

        // Генерация slug
        static::saving(function ($page) {
            if (empty($page->slug)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
        });

        // 👉 Сброс кеша меню при сохранении
        static::saved(function () {
            cache()->forget('menu_pages');
        });

        // 👉 Сброс кеша при удалении
        static::deleted(function () {
            cache()->forget('menu_pages');
        });
    }


}
