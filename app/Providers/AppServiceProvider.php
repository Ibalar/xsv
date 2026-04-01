<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
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
        // View Composer для основных страниц - гарантирует наличие данных до рендеринга дочерних шаблонов
        View::composer([
            'layouts.main',
            'products.show',
            'catalog.show',
            'pages.show',
            'pages.contacts'
        ], function ($view) {
            static $alreadyComposed = false;
            if ($alreadyComposed) {
                return;
            }
            $alreadyComposed = true;

            // ===== SEO данные =====
            $contacts = SiteSetting::contacts();
            $businessInfo = SiteSetting::normalizeArray(SiteSetting::getCached('business_info', []));
            $socialLinks = SiteSetting::normalizeArray(SiteSetting::getCached('social_links', []));

            View::share('globalSeo', [
                'site_name' => config('app.name', 'XSV.BY'),
                'site_url' => rtrim((string) url('/'), '/'),
                'default_image' => asset('assets/app-icons/icon-180x180.png'),
                'organization_name' => $businessInfo['company_name'] ?? config('app.name', 'XSV.BY'),
                'contacts' => $contacts,
                'social_links' => array_values(array_filter($socialLinks)),
            ]);

            // ===== Настройки сайта для top-bar и footer =====
            $siteSettings = cache()->remember('site_settings_all', 3600, function () use ($contacts, $socialLinks, $businessInfo) {
                return [
                    'phones' => $contacts,
                    'email' => $contacts['email'] ?? null,
                    'address' => $contacts['address'] ?? null,
                    'social_links' => $socialLinks,
                    'business_info' => $businessInfo,
                ];
            });

            View::share('siteSettings', $siteSettings);

            // ===== Категории для меню (переиспользуем кэш) =====
            $categories = cache()->remember('menu_categories', 3600, function () {
                return Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->ordered()
                    ->get();
            });

            View::share('headerCategories', $categories);

            // ===== Колонки меню для хедера =====
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

            View::share('menuColumns', $columns);

            // ===== Страницы для меню =====
            $menuPages = cache()->remember('menu_pages', 3600, function () {
                return Page::query()
                    ->where('is_active', true)
                    ->where('in_menu', true)
                    ->orderBy('sort')
                    ->orderBy('title')
                    ->get();
            });

            View::share('menuPages', $menuPages);

            // ===== Категории для футера =====
            $footerCategories = cache()->remember('footer_categories', 3600, function () {
                return Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->ordered()
                    ->get();
            });

            View::share('footerCategories', $footerCategories);

            // ===== Хлебные крошки =====
            $items = [];
            if (! $view->offsetExists('breadcrumbs')) {
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
                elseif (isset($view->category)) {
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

                // PAGE
                elseif (isset($view->page)) {
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

                elseif ($view->name() === 'pages.contacts') {
                    $items[] = [
                        'title' => 'Главная',
                        'url' => route('home'),
                    ];

                    $items[] = [
                        'title' => 'Контакты',
                        'url' => null,
                    ];
                }
            } else {
                $items = $view->breadcrumbs;
            }

            View::share('breadcrumbs', $items);
        });
    }
}
