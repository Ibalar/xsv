<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Image;
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
            Tabs::make([
                Tab::make('Основные данные', [
                    ID::make(),
                    Grid::make([
                        Column::make(
                            [
                                Text::make('Название', 'name')
                                    ->when(
                                        fn() => $this->getResource()->isCreateFormPage(),
                                        fn(Text $field) => $field->reactive(),
                                        fn(Text $field) => $field
                                    )
                                    ->required(),
                            ],
                            colSpan: 6
                        ),
                        Column::make(
                            [
                                Slug::make('Slug', 'slug')
                                    ->unique()
                                    ->locked()
                                    ->when(
                                        fn() => $this->getResource()->isCreateFormPage(),
                                        fn(Slug $field) => $field->from('name')->live(),
                                        fn(Slug $field) => $field->readonly()
                                    ),
                            ],
                            colSpan: 6
                        ),
                        Column::make(
                            [
                                BelongsTo::make('Родитель','parent', resource: CategoryResource::class,)
                                    ->nullable()
                                    ->searchable(),
                            ],
                            colSpan: 6
                        ),
                        Column::make(
                            [
                                Image::make('Изображение', 'image')
                                    ->disk(moonshineConfig()->getDisk())
                                    ->dir('categories')
                                    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                                    ->removable(),
                            ],
                            colSpan: 6
                        ),
                    ]),
                    Flex::make([
                        Number::make('Сортировка', 'sort_order')->default(0),
                        Switcher::make('Активна', 'is_active')->default(true),
                    ])
                        ->justifyAlign('between')
                        ->itemsAlign('start'),

                ]),
                Tab::make('Описание', [
                    TinyMce::make('Описание', 'description'),
                ]),
                Tab::make('СЕО', [
                    Text::make('SEO Title', 'seo_title'),
                    Text::make('SEO H1', 'seo_h1'),
                    Textarea::make('SEO Description', 'seo_description'),
                ]),
            ]),

        ];
    }
}
