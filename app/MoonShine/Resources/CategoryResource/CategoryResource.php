<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource;

use App\Models\Category;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryDetailPage;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryFormPage;
use App\MoonShine\Resources\CategoryResource\Pages\CategoryIndexPage;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Category, CategoryIndexPage, CategoryFormPage, CategoryDetailPage>
 */
class CategoryResource extends TreeResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Category::class;

    protected string $column = 'name';

    protected string $sortColumn = 'sort_order';

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

    public function treeKey(): ?string
    {
        return 'parent_id'; // Foreign key for parent-child relationship
    }

    public function sortKey(): string
    {
        return 'sort_order'; // Column for sorting
    }

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Slug::make('Slug', 'slug'),
        ];
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::VIEW);
    }

    public function compactTree(): bool {
        return true;
    }

}
