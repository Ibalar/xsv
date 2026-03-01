<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use Illuminate\Contracts\Database\Eloquent\Builder;
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
        return [
            BelongsTo::make(
                'Категория',
                'category',
                resource: CategoryResource::class,
            )->valuesQuery(
                static fn (Builder $q) => $q->active()->select(['id', 'name'])
            ),

            BelongsTo::make(
                'Поставщик',
                'supplier',
                resource: SupplierResource::class,
            )->valuesQuery(
                static fn (Builder $q) => $q->active()->select(['id', 'name'])
            ),

            BelongsTo::make(
                'Страна',
                'country',
                resource: CountryResource::class,
            )->valuesQuery(
                static fn (Builder $q) => $q->active()->select(['id', 'name'])
            ),

            Switcher::make('Активен', 'is_active'),
            Switcher::make('Хит', 'is_featured'),
            Switcher::make('Новинка', 'is_new'),
            Switcher::make('Бестселлер', 'is_bestseller'),

            Text::make('Артикул', 'sku'),
            Text::make('Название', 'name'),
        ];
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
