<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeResource\Pages;

use App\MoonShine\Resources\AttributeResource\AttributeResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<AttributeResource>
 */
final class AttributeIndexPage extends IndexPage
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

            Text::make('Slug', 'slug')
                ->sortable(),

            Text::make('Тип', 'type')
                ->sortable(),

            Switcher::make('Фильтруемый', 'is_filterable')
                ->sortable(),

            Number::make('Сортировка', 'sort_order')
                ->sortable(),
        ];
    }
}
