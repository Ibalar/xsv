<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<ProductResource>
 */
final class ProductDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Основное', [
                ID::make(),
                Text::make('Название', 'name'),
                Text::make('Slug', 'slug'),
                Text::make('Артикул', 'sku'),
                BelongsTo::make(
                    'Категория',
                    'category',
                    resource: CategoryResource::class,
                ),
                BelongsTo::make(
                    'Поставщик',
                    'supplier',
                    resource: SupplierResource::class,
                ),
                BelongsTo::make(
                    'Страна',
                    'country',
                    resource: CountryResource::class,
                ),
            ]),

            Box::make('Цены и остаток', [
                Number::make('Цена', 'price')
                    ->formatted(fn ($value) => number_format($value, 2, ',', ' ') . ' ₽'),
                Number::make('Старая цена', 'old_price')
                    ->formatted(fn ($value) => $value ? number_format($value, 2, ',', ' ') . ' ₽' : '-'),
                Number::make('Оптовая цена', 'wholesale_price')
                    ->formatted(fn ($value) => $value ? number_format($value, 2, ',', ' ') . ' ₽' : '-'),
                Number::make('Мин. кол-во для опта', 'wholesale_min_quantity'),
                Number::make('Остаток', 'stock'),
                Switcher::make('В наличии', 'in_stock'),
            ]),

            Box::make('Статусы', [
                Switcher::make('Активен', 'is_active'),
                Switcher::make('Хит', 'is_featured'),
                Switcher::make('Новинка', 'is_new'),
                Switcher::make('Бестселлер', 'is_bestseller'),
                Number::make('Сортировка', 'sort_order'),
                Number::make('Просмотры', 'views'),
            ]),

            Box::make('Контент', [
                Image::make('Изображение', 'image'),
                Textarea::make('Краткое описание', 'short_description'),
                Textarea::make('Описание', 'description'),
            ]),

            Box::make('SEO', [
                Text::make('SEO Title', 'seo_title'),
                Text::make('SEO H1', 'seo_h1'),
                Textarea::make('SEO Description', 'seo_description'),
            ]),

            Box::make('Атрибуты', [
                Textarea::make('Атрибуты', 'attributes')
                    ->formatted(fn ($value) => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value),
            ]),
        ];
    }
}
