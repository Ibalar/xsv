<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SiteSetting;
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
        // View Composer для настроек сайта (контакты, соц. сети, реквизиты)
        View::composer(['partials.top-bar', 'partials.footer'], function ($view) {
            $siteSettings = cache()->remember('site_settings_all', 3600, function () {
                $contacts = SiteSetting::getCached('contacts', []);

                return [
                    'phones' => $contacts,
                    'email' => $contacts['email'] ?? null,
                    'address' => $contacts['address'] ?? null,
                    'social_links' => SiteSetting::getCached('social_links', []),
                    'business_info' => SiteSetting::getCached('business_info', []),
                ];
            });

            $view->with('siteSettings', $siteSettings);
        });
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

            // 👉 ДОБАВИЛИ СТРАНИЦЫ
            $menuPages = cache()->remember('menu_pages', 3600, function () {
                return \App\Models\Page::query()
                    ->where('is_active', true)
                    ->where('in_menu', true)
                    ->orderBy('sort')
                    ->orderBy('title')
                    ->get();
            });

            $view->with('menuPages', $menuPages);

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

            if ($view->offsetExists('breadcrumbs')) {
                return;
            }

            $items = [];

            // PRODUCT
            if (isset($view->product)) {
                $product = $view->product;

                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                if ($product->category) {
                    foreach ($product->category->getAncestorsAndSelf() as $cat) {
                        $items[] = [
                            'title' => $cat->name,
                            'url' => route('catalog.show', $cat->getFullPath()),
                        ];
                    }
                }

                $items[] = [
                    'title' => $product->name,
                    'url' => null,
                ];
            }

            // CATEGORY
            if (isset($view->category)) {
                $category = $view->category;

                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                foreach ($category->getAncestorsAndSelf() as $cat) {
                    $items[] = [
                        'title' => $cat->name,
                        'url' => route('catalog.show', $cat->getFullPath()),
                    ];
                }
            }

            // PAGE ✅
            if (isset($view->page)) {
                $page = $view->page;

                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                $items[] = [
                    'title' => $page->title,
                    'url' => null,
                ];
            }

            if ($view->name() === 'pages.contacts') {
                $items[] = [
                    'title' => 'Главная',
                    'url' => route('home'),
                ];

                $items[] = [
                    'title' => 'Контакты',
                    'url' => null,
                ];
            }

            $view->with('breadcrumbs', $items);
        });
    }
}
