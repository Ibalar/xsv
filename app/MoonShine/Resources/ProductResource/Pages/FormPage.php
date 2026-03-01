<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\ProductResource\ProductResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ProductResource, Product>
 */
final class FormPage extends FormPage
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
                            ->required()
                            ->onAfterWrite(
                                static fn ($field) => $field->autoGenerateSlug('slug')
                            ),

                        Slug::make('Slug', 'slug')
                            ->from('name')
                            ->required(),

                        Flex::make([
                            BelongsTo::make(
                                'Категория',
                                'category',
                                formatted: static fn ($model) => $model?->name,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Поставщик',
                                'supplier',
                                formatted: static fn ($model) => $model?->name,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Страна',
                                'country',
                                formatted: static fn ($model) => $model?->name,
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
                    ])->icon('catalog'),

                    Tab::make('Контент', [
                        Image::make('Изображение', 'image')
                            ->disk(moonshineConfig()->getDisk())
                            ->dir('products')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                            ->removable(),

                        Textarea::make('Краткое описание', 'short_description')
                            ->rows(3),

                        Textarea::make('Описание', 'description')
                            ->rows(6)
                            ->cast(fn ($value) => htmlspecialchars($value ?? ''))
                            ->unwrap(),
                    ])->icon('document-text'),

                    Tab::make('Атрибуты', [
                        Box::make('Дополнительные характеристики', [
                            Textarea::make('Атрибуты (JSON)', 'attributes')
                                ->hint('JSON формат: {"цвет": "красный", "размер": "M"}')
                                ->rows(5),
                        ]),
                    ])->icon('cog'),

                    Tab::make('SEO', [
                        Box::make('Мета-теги', [
                            Text::make('SEO Title', 'seo_title'),

                            Text::make('SEO H1', 'seo_h1'),

                            Textarea::make('SEO Description', 'seo_description')
                                ->rows(3),
                        ]),
                    ])->icon('globe'),
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
        ];
    }
}
