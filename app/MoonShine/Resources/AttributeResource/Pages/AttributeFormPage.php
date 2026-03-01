<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeResource\Pages;

use App\Models\Attribute;
use App\MoonShine\Resources\AttributeResource\AttributeResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<AttributeResource, Attribute>
 */
final class AttributeFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Основное', [
                ID::make(),

                Text::make('Название', 'name')
                    ->required(),

                Slug::make('Slug', 'slug')
                    ->from('name')
                    ->required(),

                Select::make('Тип', 'type')
                    ->options(Attribute::getTypes())
                    ->required()
                    ->default(Attribute::TYPE_TEXT),

                Switcher::make('Фильтруемый', 'is_filterable')
                    ->default(false),

                Number::make('Сортировка', 'sort_order')
                    ->default(0),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => 'required',
            'slug' => 'nullable',
            'type' => 'required',
            'is_filterable' => 'nullable',
            'sort_order' => 'nullable',
        ];
    }
}
