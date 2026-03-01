<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<CategoryResource>
 */
final class CategoryDetailPage extends DetailPage
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
            Image::make('Изображение', 'image'),
            BelongsTo::make(
                'Родитель',
                'parent',
                resource: CategoryResource::class,
            )->nullable(),
            Switcher::make('Активна', 'is_active'),
            Number::make('Сортировка', 'sort_order'),
            Textarea::make('Описание', 'description'),
            Text::make('SEO Title', 'seo_title'),
            Text::make('SEO H1', 'seo_h1'),
            Textarea::make('SEO Description', 'seo_description'),
        ];
    }
}
