<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductAttributeValue\Support;

use App\Models\AttributeValue;
use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;

trait HasProductAttributeFields
{
    protected function makeProductAttributeField(bool $withMarkers = false): BelongsTo
    {
        $field = BelongsTo::make(
            'Атрибут',
            'attribute',
            resource: AttributeResource::class
        )
            ->creatable()
            ->searchable()
            ->required()
            ->valuesQuery(
                static fn (Builder $query) => $query->active()->ordered()->select(['id', 'name'])
            );

        if ($withMarkers) {
            $field->customAttributes([
                'data-product-attribute-field' => 'true',
            ]);
        }

        return $field;
    }

    protected function makeProductAttributeValueField(bool $withMarkers = false): BelongsTo
    {
        $field = BelongsTo::make(
            'Значение',
            'attributeValue',
            resource: AttributeValueResource::class
        )
            ->creatable()
            ->required()
            ->asyncSearch(
                column: 'value',
                searchQuery: static function (
                    Builder $query,
                    string $term,
                    RelationModelFieldRequest $request
                ): Builder {
                    $attributeId = $request->integer('product_attribute_selected_attribute');

                    if ($attributeId <= 0) {
                        return $query->whereRaw('1 = 0');
                    }

                    return $query
                        ->where('attribute_id', $attributeId)
                        ->orderBy('value');
                },
                formatted: static fn (AttributeValue $value): string => $value->value,
                limit: 100,
            );

        if ($withMarkers) {
            $field->customAttributes([
                'data-product-attribute-value-field' => 'true',
            ]);
        }

        return $field;
    }
}
