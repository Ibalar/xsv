<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
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

            $categories = cache()->remember('menu_categories', 3600, function () {
                return \App\Models\Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->ordered()
                    ->get();
            });

            // 👉 для мобильного меню
            $view->with('headerCategories', $categories);

            $footerCategories = cache()->remember('footer_categories', 3600, function () {
                return \App\Models\Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->ordered()
                    ->get();
            });

            $view->with('footerCategories', $footerCategories);

            // 👉 логика колонок
            $columns = [];

            $count = $categories->count();

            if ($count <= 4) {
                foreach ($categories as $category) {
                    $columns[] = collect([$category]);
                }
            } else {
                $columns[] = collect([$categories[0]]);
                $columns[] = collect([$categories[1]]);

                $rest = $categories->slice(2)->values();
                $chunks = $rest->chunk(ceil($rest->count() / 2));

                $columns[] = $chunks[0] ?? collect();
                $columns[] = $chunks[1] ?? collect();
            }

            $view->with('menuColumns', $columns);
        });

        View::composer('*', function ($view) {

            $items = [];

            // Если есть товар на странице
            if (isset($view->product)) {
                $product = $view->product;

                // Главная
                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                // Категории
                if ($product->category) {
                    $categories = $product->category->getAncestorsAndSelf();

                    foreach ($categories as $cat) {
                        $items[] = [
                            'title' => $cat->name,
                            'url' => route('catalog.show', $cat->getFullPath()), // полный путь для SEO
                        ];
                    }
                }

                // Текущий товар
                $items[] = [
                    'title' => $product->name,
                    'url' => null,
                ];

            }

            // Если есть категория на странице (например, раздел каталога)
            if (isset($view->category)) {
                $category = $view->category;

                // Главная
                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                // Все родители + текущая категория
                foreach ($category->getAncestorsAndSelf() as $cat) {
                    $items[] = [
                        'title' => $cat->name,
                        'url' => route('catalog.show', $cat->getFullPath()),
                    ];
                }
            }

            $view->with('breadcrumbs', $items);
        });
    }
}
