<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeResource\Pages;

use App\MoonShine\Resources\AttributeResource\AttributeResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<AttributeResource>
 */
final class AttributeDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Text::make('Slug', 'slug'),
            Text::make('Тип', 'type'),
            Switcher::make('Фильтруемый', 'is_filterable'),
            Number::make('Сортировка', 'sort_order'),
        ];
    }
}
