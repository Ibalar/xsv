<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SupplierResource\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<SupplierResource, Supplier>
 */
final class SupplierFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Основное', [
                ID::make(),
                Text::make('Название', 'name')->required(),
                Slug::make('Slug', 'slug')->from('name')->required(),
                Textarea::make('Описание', 'description'),
                Switcher::make('Активен', 'is_active')->default(true),
            ]),
        ];
    }
}
