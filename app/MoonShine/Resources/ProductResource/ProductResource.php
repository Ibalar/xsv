<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource;

use App\Models\Product;
use App\MoonShine\Resources\ProductResource\Pages\DetailPage;
use App\MoonShine\Resources\ProductResource\Pages\FormPage;
use App\MoonShine\Resources\ProductResource\Pages\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Product, IndexPage, FormPage, DetailPage>
 */
#[Icon('shopping-bag')]
class ProductResource extends ModelResource
{
    protected string $model = Product::class;

    protected string $column = 'name';

    protected array $with = ['category', 'supplier', 'country'];

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Товары';
    }

    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
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
}
