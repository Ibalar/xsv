<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeValueResource;

use App\Models\AttributeValue;
use App\MoonShine\Resources\AttributeValueResource\Pages\AttributeValueDetailPage;
use App\MoonShine\Resources\AttributeValueResource\Pages\AttributeValueFormPage;
use App\MoonShine\Resources\AttributeValueResource\Pages\AttributeValueIndexPage;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<AttributeValue, AttributeValueIndexPage, AttributeValueFormPage, AttributeValueDetailPage>
 */
class AttributeValueResource extends ModelResource
{
    protected string $model = AttributeValue::class;

    protected string $column = 'value';

    protected array $with = ['attribute'];

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Значения атрибутов';
    }

    protected function pages(): array
    {
        return [
            AttributeValueIndexPage::class,
            AttributeValueFormPage::class,
            AttributeValueDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'value',
        ];
    }
}
