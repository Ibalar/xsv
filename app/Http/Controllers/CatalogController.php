<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        // Корневые категории (без родителя)
        $categories = \App\Models\Category::query()
            ->whereNull('parent_id')
            ->active()
            ->ordered()
            ->with(['children' => fn($q) => $q->active()->ordered()])
            ->withCount(['children' => fn($q) => $q->active()]) // чтобы знать есть ли вложенные
            ->get();

        // Хлебные крошки
        $breadcrumbs = [
            [
                'name' => 'Главная',
                'url' => route('home'),
            ],
            [
                'name' => 'Каталог',
                'url' => null, // текущая страница
            ],
        ];

        return view('catalog.index', compact('categories', 'breadcrumbs'));
    }

    public function show(string $path, Request $request)
    {
        // Разбиваем путь: parent/child/subchild
        $slugs = explode('/', $path);

        // Ищем категорию по последнему slug
        $category = Category::query()
            ->where('slug', end($slugs))
            ->with(['children' => fn($q) => $q->active()->ordered()])
            ->firstOrFail();

        // 🔥 проверка полного пути (SEO)
        if ($category->getFullPath() !== $path) {
            abort(404);
        }

        // Получаем все id категории + потомков
        $categoryIds = $category->getAllDescendantIds();

        // Базовый запрос товаров (только активные)
        $productsQuery = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->active();

        // ✅ Загружаем фильтруемые атрибуты (НУЖНО ДЛЯ SLUG)
        $filterAttributes = Attribute::query()
            ->active()
            ->filterable()
            ->ordered()
            ->with(['attributeValues' => function ($q) {
                $q->orderBy('value');
            }])
            ->get();

        // ✅ НОВАЯ ЛОГИКА ФИЛЬТРОВ ПО SLUG
        $filters = [];

        foreach ($filterAttributes as $attribute) {
            $values = $request->get($attribute->slug);

            if (!empty($values)) {

                // сохраняем для Blade (чтобы работали чекбоксы и активные фильтры)
                $filters[$attribute->slug] = (array)$values;

                $productsQuery->whereHas('productAttributeValues', function ($q) use ($attribute, $values) {
                    $q->where('attribute_id', $attribute->id)
                        ->whereHas('attributeValue', function ($q2) use ($values) {
                            $q2->whereIn('slug', $values);
                        });
                });
            }
        }

        // --- диапазон цен ---
        $priceRange = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $minPriceAll = $priceRange->min_price ?? 0;
        $maxPriceAll = $priceRange->max_price ?? 1000;

        // --- Фильтр по цене ---
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        if ($minPrice !== null) {
            $productsQuery->where('price', '>=', (float)$minPrice);
        }
        if ($maxPrice !== null) {
            $productsQuery->where('price', '<=', (float)$maxPrice);
        }

        // Сортировка
        $sort = $request->get('sort', 'Relevance');

        switch ($sort) {
            case 'Popularity':
                $productsQuery->where('is_featured', true)->latest();
                break;
            case 'Price: Low to High':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'Price: High to Low':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'Newest Arrivals':
                $productsQuery->where('is_new', true)->latest();
                break;
            case 'Alphabet':
                $productsQuery->orderBy('name', 'asc');
                break;
            default:
                $productsQuery->latest();
                break;
        }

        // Пагинация
        $products = $productsQuery->paginate(12)->withQueryString();

        // Корневые категории
        $rootCategories = Category::query()
            ->whereNull('parent_id')
            ->active()
            ->with(['children' => fn($q) => $q->active()->ordered()])
            ->get()
            ->map(function ($cat) {
                $allIds = $cat->getAllDescendantIds();
                $cat->products_count = Product::query()
                    ->whereIn('category_id', $allIds)
                    ->active()
                    ->count();
                return $cat;
            });

        // Подкатегории
        $subcategories = $category->children()->active()->ordered()->get();

        // Количество товаров
        $productsCount = $productsQuery->count();

        // ✅ обновили логику
        $hasFilters = !empty($filters) || $request->filled('min_price') || $request->filled('max_price');

        return view('catalog.show', [
            'category' => $category,
            'products' => $products,
            'breadcrumbs' => $category->getBreadcrumbs(),
            'sort' => $sort,
            'rootCategories' => $rootCategories,
            'subcategories' => $subcategories,
            'productsCount' => $productsCount,
            'hasFilters' => $hasFilters,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minPriceAll' => $minPriceAll,
            'maxPriceAll' => $maxPriceAll,
            'filterAttributes' => $filterAttributes,
            'filters' => $filters,
        ]);
    }
}
