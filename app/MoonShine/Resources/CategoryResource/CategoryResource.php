<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource;

use App\Models\Category;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryDetailPage;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryFormPage;
use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends TreeResource<Category, CategoryFormPage, CategoryDetailPage>
 */
#[Icon('folder')]
class CategoryResource extends TreeResource
{
    protected string $model = Category::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Категории';
    }

    public function treeKey(): ?string
    {
        return 'parent_id';
    }

    public function sortKey(): string
    {
        return 'sort_order';
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
