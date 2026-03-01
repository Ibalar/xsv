<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Slug;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<CategoryResource, Category>
 */
final class CategoryFormPage extends FormPage
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
                BelongsTo::make(
                    'Родитель',
                    'parent',
                    resource: CategoryResource::class,
                )->nullable()->searchable(),
                Switcher::make('Активна', 'is_active')->default(true),
                Number::make('Сортировка', 'sort_order')->default(0),
                Textarea::make('Описание', 'description'),
            ]),
            Box::make('SEO', [
                Text::make('SEO Title', 'seo_title'),
                Text::make('SEO H1', 'seo_h1'),
                Textarea::make('SEO Description', 'seo_description'),
            ]),
        ];
    }
}
