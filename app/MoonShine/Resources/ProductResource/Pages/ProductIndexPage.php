<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ProductResource>
 */
final class ProductIndexPage extends IndexPage
{
    /** @var array<int, int>|null */
    protected ?array $memoizedCategoryIds = null;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')
                ->sortable(),

            BelongsTo::make(
                'Категория',
                'category',
                resource: CategoryResource::class,
            )->badge('info'),

            BelongsToMany::make(
                'Категории',
                'categories',
                resource: CategoryResource::class,
            )->inLine(', '),

            Number::make('Цена', 'price')
                ->sortable(),
            Switcher::make('Активен', 'is_active')
                ->sortable(),
            Switcher::make('Хит', 'is_featured')
                ->updateOnPreview()
                ->sortable(),
            Switcher::make('Новинка', 'is_new')
                ->updateOnPreview()
                ->sortable(),

        ];
    }

    protected function filters(): iterable
    {
        $filters = [
            BelongsTo::make(
                'Категория',
                'category',
                resource: CategoryResource::class,
            )
                ->nullable()
                ->valuesQuery(
                    static fn (BuilderContract $q) => $q->active()->select(['id', 'name'])
                )
                ->onApply(function (Builder $query, $value): Builder {
                    if (empty($value)) {
                        return $query;
                    }

                    $categoryIds = [$value];
                    $category = Category::find($value);

                    if ($category) {
                        $categoryIds = $category->getAllDescendantIds();
                    }

                    return $query->inCategories($categoryIds);
                }),

            BelongsTo::make(
                'Поставщик',
                'supplier',
                resource: SupplierResource::class,
            )
                ->nullable()
                ->valuesQuery(
                static fn (BuilderContract $q) => $q->active()->select(['id', 'name'])
            ),

            BelongsTo::make(
                'Страна',
                'country',
                resource: CountryResource::class,
            )
                ->nullable()
                ->valuesQuery(
                static fn (BuilderContract $q) => $q->active()->select(['id', 'name'])
            ),

            Switcher::make('Активен', 'is_active'),
            Switcher::make('Хит', 'is_featured'),
            Switcher::make('Новинка', 'is_new'),
            Switcher::make('Бестселлер', 'is_bestseller'),

            Text::make('Артикул', 'sku'),
            Text::make('Название', 'name'),
        ];

        $attributeFilters = $this->getAttributeFilters();
        foreach ($attributeFilters as $filter) {
            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * @return list<FieldContract>
     */
    protected function getAttributeFilters(): iterable
    {
        $filters = [];

        $categoryIds = $this->getCategoryIdsFromRequest();

        // Get all filters data with caching
        $filtersData = $this->getFiltersData($categoryIds);
        $attributes = $filtersData['attributes'];
        $valuesData = $filtersData['values'];

        foreach ($attributes as $attribute) {
            $filter = match ($attribute->type) {
                Attribute::TYPE_SELECT => $this->createSelectFilter(
                    $attribute,
                    $valuesData[$attribute->id] ?? []
                ),
                Attribute::TYPE_BOOLEAN => $this->createBooleanFilter(
                    $attribute,
                    $valuesData[$attribute->id] ?? []
                ),
                Attribute::TYPE_NUMBER => $this->createNumberFilter($attribute),
                default => $this->createTextFilter($attribute),
            };

            if ($filter) {
                $filters[] = $filter;
            }
        }

        return $filters;
    }

    /**
     * Get category IDs from the current request filter.
     *
     * @return array<int, int>
     */
    protected function getCategoryIdsFromRequest(): array
    {
        if ($this->memoizedCategoryIds !== null) {
            return $this->memoizedCategoryIds;
        }

        $request = request();

        $categoryId = $request->input('filters.category');

        if (empty($categoryId)) {
            $this->memoizedCategoryIds = [];

            return [];
        }

        $category = Category::find($categoryId);

        if (! $category) {
            $this->memoizedCategoryIds = [];

            return [];
        }

        $this->memoizedCategoryIds = $category->getAllDescendantIds();

        return $this->memoizedCategoryIds;
    }

    /**
     * Get attributes that are used by products in the given categories.
     *
     * @param  array<int, int>  $categoryIds
     * @return \Illuminate\Database\Eloquent\Collection<int, Attribute>
     */
    protected function getAttributesForCategory(array $categoryIds): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($categoryIds)) {
            return Attribute::query()
                ->filterable()
                ->ordered()
                ->get();
        }

        // Use subquery instead of pluck('id')
        $attributeIds = ProductAttributeValue::query()
            ->whereExists(function ($query) use ($categoryIds): void {
                $query->select(DB::raw(1))
                    ->from('products')
                    ->whereColumn('product_attribute_values.product_id', 'products.id')
                    ->where(function ($q) use ($categoryIds): void {
                        $q->whereIn('products.category_id', $categoryIds)
                            ->orWhereExists(function ($subQ) use ($categoryIds): void {
                                $subQ->select(DB::raw(1))
                                    ->from('category_product')
                                    ->whereColumn('category_product.product_id', 'products.id')
                                    ->whereIn('category_product.category_id', $categoryIds);
                            });
                    });
            })
            ->distinct()
            ->pluck('attribute_id')
            ->filter()
            ->values();

        if ($attributeIds->isEmpty()) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Attribute::query()
            ->filterable()
            ->whereIn('id', $attributeIds)
            ->ordered()
            ->get();
    }

    /**
     * Get all filters data for attributes with caching.
     *
     * @param  array<int, int>  $categoryIds
     * @return array{attributes: \Illuminate\Database\Eloquent\Collection<int, Attribute>, values: array<int, array<string>>}
     */
    protected function getFiltersData(array $categoryIds): array
    {
        $cacheKey = empty($categoryIds)
            ? 'product_filters_all'
            : 'product_filters_' . implode('_', $categoryIds);

        return Cache::remember(
            $cacheKey,
            now()->addHour(),
            function () use ($categoryIds): array {
                $attributes = $this->getAttributesForCategory($categoryIds);

                if ($attributes->isEmpty()) {
                    return [
                        'attributes' => $attributes,
                        'values' => [],
                    ];
                }

                // Get filterable attribute IDs
                $attributeIds = $attributes
                    ->whereIn('type', [Attribute::TYPE_SELECT, Attribute::TYPE_BOOLEAN])
                    ->pluck('id')
                    ->values();

                if ($attributeIds->isEmpty()) {
                    return [
                        'attributes' => $attributes,
                        'values' => [],
                    ];
                }

                // Get all values for these attributes in one query
                $valuesQuery = ProductAttributeValue::query()
                    ->whereIn('attribute_id', $attributeIds)
                    ->whereNotNull('value')
                    ->where('value', '!=', '')
                    ->select(['attribute_id', 'value']);

                if (! empty($categoryIds)) {
                    $valuesQuery->whereExists(function ($query) use ($categoryIds): void {
                        $query->select(DB::raw(1))
                            ->from('products')
                            ->whereColumn('product_attribute_values.product_id', 'products.id')
                            ->where(function ($q) use ($categoryIds): void {
                                $q->whereIn('products.category_id', $categoryIds)
                                    ->orWhereExists(function ($subQ) use ($categoryIds): void {
                                        $subQ->select(DB::raw(1))
                                            ->from('category_product')
                                            ->whereColumn('category_product.product_id', 'products.id')
                                            ->whereIn('category_product.category_id', $categoryIds);
                                    });
                            });
                    });
                }

                $values = $valuesQuery
                    ->get()
                    ->groupBy('attribute_id')
                    ->map(fn ($group) => $group->pluck('value')->unique()->values()->all())
                    ->all();

                return [
                    'attributes' => $attributes,
                    'values' => $values,
                ];
            }
        );
    }

    /**
     * @param  list<string>  $values
     */
    protected function createSelectFilter(Attribute $attribute, array $values): ?Select
    {
        if ($values === []) {
            return null;
        }

        $options = collect($values)
            ->mapWithKeys(static fn ($value) => [$value => $value])
            ->toArray();

        return Select::make($attribute->name, "attribute_{$attribute->slug}")
            ->options($options)
            ->nullable()
            ->multiple()
            ->customAttributes(['data-attribute-id' => $attribute->id])
            ->onApply(function (Builder $query, $value) use ($attribute): Builder {
                if (empty($value)) {
                    return $query;
                }

                $values = is_array($value) ? $value : [$value];

                // Optimized: use direct where instead of nested whereHas
                return $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $values): void {
                    $q->where('attribute_id', $attribute->id)
                        ->whereIn('value', $values);
                });
            });
    }

    /**
     * @param  list<string>  $values
     */
    protected function createBooleanFilter(Attribute $attribute, array $values): ?Select
    {
        $options = [];

        if (in_array('1', $values, true)) {
            $options['1'] = 'Да';
        }

        if (in_array('0', $values, true)) {
            $options['0'] = 'Нет';
        }

        if ($options === []) {
            return null;
        }

        return Select::make($attribute->name, "attribute_{$attribute->slug}")
            ->options($options)
            ->nullable()
            ->customAttributes(['data-attribute-id' => $attribute->id])
            ->onApply(function (Builder $query, $value) use ($attribute): Builder {
                if ($value === null || $value === '') {
                    return $query;
                }

                // Optimized: use direct where instead of nested whereHas
                return $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $value): void {
                    $q->where('attribute_id', $attribute->id)
                        ->where('value', $value === '1' ? '1' : '0');
                });
            });
    }

    protected function createNumberFilter(Attribute $attribute): Text
    {
        return Text::make($attribute->name, "attribute_{$attribute->slug}")
            ->customAttributes(['type' => 'number', 'data-attribute-id' => $attribute->id])
            ->onApply(function (Builder $query, $value) use ($attribute): Builder {
                if (empty($value)) {
                    return $query;
                }

                // Optimized: use direct where instead of nested whereHas
                return $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $value): void {
                    $q->where('attribute_id', $attribute->id)
                        ->where('value', $value);
                });
            });
    }

    protected function createTextFilter(Attribute $attribute): Text
    {
        return Text::make($attribute->name, "attribute_{$attribute->slug}")
            ->customAttributes(['data-attribute-id' => $attribute->id])
            ->onApply(function (Builder $query, $value) use ($attribute): Builder {
                if (empty($value)) {
                    return $query;
                }

                // Optimized: use direct where instead of nested whereHas
                return $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $value): void {
                    $q->where('attribute_id', $attribute->id)
                        ->where('value', 'LIKE', "%{$value}%");
                });
            });
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
