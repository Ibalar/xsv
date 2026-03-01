<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeValueResource\Pages;

use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<AttributeValueResource>
 */
final class AttributeValueDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),

            BelongsTo::make(
                'Атрибут',
                'attribute',
                resource: AttributeResource::class,
            ),

            Text::make('Значение', 'value'),
        ];
    }
}
