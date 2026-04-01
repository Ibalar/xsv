<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
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
        return [
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

            Switcher::make('Активен', 'is_active'),
            Switcher::make('Хит', 'is_featured'),
            Switcher::make('Новинка', 'is_new'),
            Switcher::make('Бестселлер', 'is_bestseller'),
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
