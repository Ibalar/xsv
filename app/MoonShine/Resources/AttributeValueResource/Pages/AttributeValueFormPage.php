<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeValueResource\Pages;

use App\Models\AttributeValue;
use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<AttributeValueResource, AttributeValue>
 */
final class AttributeValueFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Основное', [
                ID::make(),

                BelongsTo::make(
                    'Атрибут',
                    'attribute',
                    resource: AttributeResource::class,
                )->required()->searchable(),

                Text::make('Значение', 'value')
                    ->required(),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required',
        ];
    }
}
