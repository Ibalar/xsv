<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<CategoryResource>
 */
final class CategoryIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->sortable()->searchable(),
            Text::make('Slug', 'slug')->sortable(),
            Switcher::make('Активна', 'is_active'),
            Number::make('Сортировка', 'sort_order')->sortable(),
        ];
    }
}
