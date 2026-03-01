<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource;

use App\Models\Category;
use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryDetailPage;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryFormPage;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryIndexPage;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Category, CategoryIndexPage, CategoryFormPage, CategoryDetailPage>
 */
#[Icon('folder')]
class CategoryResource extends ModelResource
{
    protected string $model = Category::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Категории';
    }

    protected function pages(): array
    {
        return [
            CategoryIndexPage::class,
            CategoryFormPage::class,
            CategoryDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'slug',
        ];
    }
}
