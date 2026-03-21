<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource;

use App\Models\Product;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\ProductResource\Pages\ProductDetailPage;
use App\MoonShine\Resources\ProductResource\Pages\ProductFormPage;
use App\MoonShine\Resources\ProductResource\Pages\ProductIndexPage;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Product, ProductIndexPage, ProductFormPage, ProductDetailPage>
 */

class ProductResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

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

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Slug::make('Slug', 'slug'),
            Text::make('SEO Title', 'seo_title'),
            Text::make('Артикул', 'sku'),
            Switcher::make('Активен', 'is_active'),
            Number::make('Цена', 'price'),
            Image::make('Изображение', 'image')
                ->multiple()
                ->disk(moonshineConfig()->getDisk())
                ->dir('products')
                ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                ->removable(),
            TinyMce::make('Описание', 'description'),
            BelongsTo::make(
                'Категория',
                'category',
                resource: CategoryResource::class,
            ),
        ];
    }

}
