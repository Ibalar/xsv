<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
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

            Number::make('Цена', 'price')
                ->sortable(),

            Number::make('Старая цена', 'old_price'),

            Text::make('Артикул', 'sku'),

            Number::make('Остаток', 'stock')
                ->sortable(),

            Switcher::make('Активен', 'is_active')
                ->sortable(),

            Switcher::make('Хит', 'is_featured')
                ->sortable(),

            Switcher::make('Новинка', 'is_new')
                ->sortable(),

            Number::make('Просмотры', 'views')
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

                    return $query->whereIn('category_id', $categoryIds);
                }),

            BelongsTo::make(
                'Поставщик',
                'supplier',
                resource: SupplierResource::class,
            )->valuesQuery(
                static fn (BuilderContract $q) => $q->active()->select(['id', 'name'])
            ),

            BelongsTo::make(
                'Страна',
                'country',
                resource: CountryResource::class,
            )->valuesQuery(
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

        $attributes = $this->getAttributesForCategory($categoryIds);

        foreach ($attributes as $attribute) {
            $filter = match ($attribute->type) {
                Attribute::TYPE_SELECT => $this->createSelectFilter($attribute, $categoryIds),
                Attribute::TYPE_BOOLEAN => $this->createBooleanFilter($attribute, $categoryIds),
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
        $request = request();

        $categoryId = $request->input('filters.category');

        if (empty($categoryId)) {
            return [];
        }

        $category = Category::find($categoryId);

        if (! $category) {
            return [];
        }

        return $category->getAllDescendantIds();
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

        $productIds = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->pluck('id');

        if ($productIds->isEmpty()) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $attributeIds = ProductAttributeValue::query()
            ->whereIn('product_id', $productIds)
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
     * @return list<string>
     */
    protected function getAttributeValues(Attribute $attribute, array $categoryIds = []): array
    {
        $query = ProductAttributeValue::query()
            ->where('attribute_id', $attribute->id);

        if (! empty($categoryIds)) {
            $productIds = Product::query()
                ->whereIn('category_id', $categoryIds)
                ->pluck('id');

            if ($productIds->isEmpty()) {
                return [];
            }

            $query->whereIn('product_id', $productIds);
        }

        return $query->distinct()
            ->pluck('value')
            ->filter(static fn ($value) => $value !== null && $value !== '')
            ->map(static fn ($value) => (string) $value)
            ->unique()
            ->values()
            ->all();
    }

    protected function createSelectFilter(Attribute $attribute, array $categoryIds = []): ?Select
    {
        $values = $this->getAttributeValues($attribute, $categoryIds);

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

                return $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $values): void {
                    $q->where('attribute_id', $attribute->id)
                        ->whereIn('value', $values);
                });
            });
    }

    protected function createBooleanFilter(Attribute $attribute, array $categoryIds = []): ?Select
    {
        $values = $this->getAttributeValues($attribute, $categoryIds);

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
