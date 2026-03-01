<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource;

use App\Models\AttributeValue;
use App\Models\Product;
use App\MoonShine\Resources\ProductResource\Pages\ProductDetailPage;
use App\MoonShine\Resources\ProductResource\Pages\ProductFormPage;
use App\MoonShine\Resources\ProductResource\Pages\ProductIndexPage;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<Product, ProductIndexPage, ProductFormPage, ProductDetailPage>
 */

class ProductResource extends ModelResource
{
    protected string $model = Product::class;

    protected string $column = 'name';

    protected array $with = ['category', 'supplier', 'country', 'attributeValueOptions'];

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Товары';
    }

    protected function pages(): array
    {
        return [
            ProductIndexPage::class,
            ProductFormPage::class,
            ProductDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'sku',
        ];
    }

    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        $product = $item->getOriginal();
        
        $attributes = request()->input('attributes', []);

        $sync = [];

        foreach ($attributes as $attributeRow) {
            if (!empty($attributeRow['attribute_id']) && !empty($attributeRow['value_ids'])) {
                foreach ($attributeRow['value_ids'] as $valueId) {
                    $attributeValue = AttributeValue::find($valueId);
                    if ($attributeValue) {
                        $sync[$valueId] = [
                            'attribute_id' => $attributeRow['attribute_id'],
                            'value' => $attributeValue->value,
                        ];
                    }
                }
            }
        }

        $product->productAttributeValues()->delete();

        foreach ($sync as $valueId => $pivotData) {
            $product->productAttributeValues()->create([
                'attribute_id' => $pivotData['attribute_id'],
                'attribute_value_id' => $valueId,
                'value' => $pivotData['value'],
            ]);
        }

        return $item;
    }
}
