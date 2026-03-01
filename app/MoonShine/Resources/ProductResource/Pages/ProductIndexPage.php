<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Attribute;
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
            )->valuesQuery(
                static fn (BuilderContract $q) => $q->active()->select(['id', 'name'])
            ),

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

        $attributes = Attribute::query()
            ->filterable()
            ->ordered()
            ->get();

        foreach ($attributes as $attribute) {
            $filter = match ($attribute->type) {
                Attribute::TYPE_SELECT => $this->createSelectFilter($attribute),
                Attribute::TYPE_BOOLEAN => $this->createBooleanFilter($attribute),
                Attribute::TYPE_NUMBER => $this->createNumberFilter($attribute),
                default => $this->createTextFilter($attribute),
            };

            $filters[] = $filter;
        }

        return $filters;
    }

    protected function createSelectFilter(Attribute $attribute): Select
    {
        $values = ProductAttributeValue::query()
            ->where('attribute_id', $attribute->id)
            ->distinct()
            ->pluck('value', 'value')
            ->toArray();

        return Select::make($attribute->name, "attribute_{$attribute->slug}")
            ->options($values)
            ->nullable()
            ->customAttributes(['data-attribute-id' => $attribute->id])
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

    protected function createBooleanFilter(Attribute $attribute): Select
    {
        return Select::make($attribute->name, "attribute_{$attribute->slug}")
            ->options([
                '1' => 'Да',
                '0' => 'Нет',
            ])
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
