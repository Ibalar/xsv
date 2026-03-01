<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeValueResource\Pages;

use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<AttributeValueResource>
 */
final class AttributeValueIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(
                'Атрибут',
                'attribute',
                resource: AttributeResource::class,
            )->sortable(),

            Text::make('Значение', 'value')
                ->sortable(),
        ];
    }
}
