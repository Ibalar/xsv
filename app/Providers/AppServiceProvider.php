<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

            $categories = \App\Models\Category::query()
                ->active()
                ->whereNull('parent_id')
                ->with('childrenRecursive')
                ->ordered()
                ->get();

            $columns = [];

            $count = $categories->count();

            if ($count <= 4) {
                // просто по одной категории в колонку
                foreach ($categories as $category) {
                    $columns[] = collect([$category]);
                }
            } else {
                // 🔥 сложная логика

                // первые 2 категории — отдельные колонки
                $columns[] = collect([$categories[0]]);
                $columns[] = collect([$categories[1]]);

                // остальные делим пополам
                $rest = $categories->slice(2)->values();

                $chunks = $rest->chunk(ceil($rest->count() / 2));

                $columns[] = $chunks[0] ?? collect();
                $columns[] = $chunks[1] ?? collect();
            }

            $view->with('menuColumns', $columns);
        });
    }
}
