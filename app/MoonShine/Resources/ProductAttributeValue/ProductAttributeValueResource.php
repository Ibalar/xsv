<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductAttributeValue;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductAttributeValue;
use App\MoonShine\Resources\ProductAttributeValue\Pages\ProductAttributeValueIndexPage;
use App\MoonShine\Resources\ProductAttributeValue\Pages\ProductAttributeValueFormPage;
use App\MoonShine\Resources\ProductAttributeValue\Pages\ProductAttributeValueDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<ProductAttributeValue, ProductAttributeValueIndexPage, ProductAttributeValueFormPage, ProductAttributeValueDetailPage>
 */
class ProductAttributeValueResource extends ModelResource
{
    protected string $model = ProductAttributeValue::class;

    protected string $title = 'Атрибуты товара';

    protected string $column = 'name';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ProductAttributeValueIndexPage::class,
            ProductAttributeValueFormPage::class,
            ProductAttributeValueDetailPage::class,
        ];
    }
}
