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
use MoonShine\Laravel\Fields\Slug;
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
                    ->when(
                        fn() => $this->getResource()->isCreateFormPage(),
                        fn(Text $field) => $field->reactive(),
                        fn(Text $field) => $field
                    )
                    ->required(),

                Slug::make('Slug', 'slug')
                    ->unique()
                    ->locked()
                    ->when(
                        fn() => $this->getResource()->isCreateFormPage(),
                        fn(Slug $field) => $field->from('value')->live(),
                        fn(Slug $field) => $field->readonly()
                    ),
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
