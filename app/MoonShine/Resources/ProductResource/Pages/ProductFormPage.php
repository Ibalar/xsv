<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\MoonShine\Resources\ProductAttributeValue\ProductAttributeValueResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Support\AlpineJs;

/**
 * @extends FormPage<ProductResource, Product>
 */
final class ProductFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make('Основное', [
                        ID::make(),

                        Text::make('Название', 'name')
                            ->when(
                                fn() => $this->getResource()->isCreateFormPage(),
                                fn(Text $field) => $field->reactive(),
                                fn(Text $field) => $field
                            )
                            ->required(),

                        Slug::make('Slug', 'slug')
                            ->unique()
                            ->locked()
                            ->when(
                                fn() => $this->getResource()->isCreateFormPage(),
                                fn(Slug $field) => $field->from('name')->live(),
                                fn(Slug $field) => $field->readonly()
                            ),

                        Flex::make([
                            BelongsTo::make(
                                'Категория',
                                'category',
                                resource: CategoryResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Поставщик',
                                'supplier',
                                resource: SupplierResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Страна',
                                'country',
                                resource: CountryResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),
                        ]),

                        Text::make('Артикул', 'sku'),

                        Flex::make([
                            Number::make('Цена', 'price')
                                ->min(0)
                                ->step(0.01)
                                ->default(0),

                            Number::make('Старая цена', 'old_price')
                                ->nullable()
                                ->min(0)
                                ->step(0.01),

                            Number::make('Оптовая цена', 'wholesale_price')
                                ->nullable()
                                ->min(0)
                                ->step(0.01),

                            Number::make('Мин. кол-во для опта', 'wholesale_min_quantity')
                                ->nullable()
                                ->min(0),
                        ]),

                        Flex::make([
                            Number::make('Остаток', 'stock')
                                ->min(0)
                                ->default(0),

                            Number::make('Сортировка', 'sort_order')
                                ->min(0)
                                ->default(0),
                        ]),

                        Flex::make([
                            Switcher::make('Активен', 'is_active')
                                ->default(true),

                            Switcher::make('Хит', 'is_featured'),

                            Switcher::make('Новинка', 'is_new'),

                            Switcher::make('Бестселлер', 'is_bestseller'),

                            Switcher::make('В наличии', 'in_stock')
                                ->default(true),
                        ]),
                    ]),

                    Tab::make('Контент', [
                        Image::make('Изображение', 'image')
                            ->disk(moonshineConfig()->getDisk())
                            ->keepOriginalFileName()
                            ->dir('products')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                            ->removable(),

                        Image::make('Галерея', 'gallery')
                            ->multiple()
                            ->disk(moonshineConfig()->getDisk())
                            ->keepOriginalFileName()
                            ->dir('products')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                            ->removable(),

                        Textarea::make('Краткое описание', 'short_description'),

                        TinyMce::make('Описание', 'description'),
                    ]),

                    Tab::make('Атрибуты', [

                            $this->getAttributesField(),

                    ]),

                    Tab::make('SEO', [
                        Box::make('Мета-теги', [
                            Text::make('SEO Title', 'seo_title'),

                            Text::make('SEO H1', 'seo_h1'),

                            Textarea::make('SEO Description', 'seo_description'),
                        ]),
                    ]),
                ]),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => 'required',
            'category_id' => 'nullable',
            'supplier_id' => 'nullable',
            'country_id' => 'nullable',
            'sku' => 'nullable',
            'price' => 'nullable',
            'old_price' => 'nullable',
            'wholesale_price' => 'nullable',
            'wholesale_min_quantity' => 'nullable',
            'stock' => 'nullable',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
            'is_new' => 'nullable',
            'is_bestseller' => 'nullable',
            'in_stock' => 'nullable',
            'sort_order' => 'nullable',
            'seo_title' => 'nullable',
            'seo_h1' => 'nullable',
            'seo_description' => 'nullable',
            'productAttributeValues' => 'nullable|array',
            'productAttributeValues.*._attribute_id' => 'required_with:productAttributeValues.*|exists:attributes,id',
            'productAttributeValues.*.attribute_value_id' => 'required_with:productAttributeValues.*|exists:attribute_values,id',
        ];
    }

    protected function getAttributesField(): RelationRepeater
    {
        return RelationRepeater::make(
            'Атрибуты',
            'productAttributeValues',
            resource: ProductAttributeValueResource::class
        )
            ->creatable()
            ->removable()
            ->fields([
                Select::make('Атрибут', '_attribute_id')
                    ->options(Attribute::query()->active()->ordered()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->required()
                    ->afterFill(function (Select $field, DataWrapperContract $data) {
                        // Set the attribute select value from the related attributeValue
                        if ($data->attributeValue && $data->attributeValue->attribute) {
                            $field->setValue($data->attributeValue->attribute_id);
                        }
                    }),

                BelongsTo::make(
                    'Значение',
                    'attributeValue',
                    resource: AttributeValueResource::class
                )
                    ->reactive()
                    ->valuesQuery(static function (Builder $query, FieldContract $field): Builder {
                        // Filter attribute values by the selected attribute
                        $attributeId = $field->getReactiveValue('_attribute_id');

                        if ($attributeId) {
                            return $query->where('attribute_id', $attributeId);
                        }

                        return $query;
                    })
                    ->searchable()
                    ->required()
                    ->with('attribute'),
            ]);
    }
}
