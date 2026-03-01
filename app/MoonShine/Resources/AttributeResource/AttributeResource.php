<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeResource;

use App\Models\Attribute;
use App\MoonShine\Resources\AttributeResource\Pages\AttributeDetailPage;
use App\MoonShine\Resources\AttributeResource\Pages\AttributeFormPage;
use App\MoonShine\Resources\AttributeResource\Pages\AttributeIndexPage;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<Attribute, AttributeIndexPage, AttributeFormPage, AttributeDetailPage>
 */
class AttributeResource extends ModelResource
{
    protected string $model = Attribute::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Атрибуты';
    }

    protected function pages(): array
    {
        return [
            AttributeIndexPage::class,
            AttributeFormPage::class,
            AttributeDetailPage::class,
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
