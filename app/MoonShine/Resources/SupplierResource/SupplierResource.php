<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SupplierResource;

use App\Models\Supplier;
use App\MoonShine\Resources\SupplierResource\Pages\SupplierDetailPage;
use App\MoonShine\Resources\SupplierResource\Pages\SupplierFormPage;
use App\MoonShine\Resources\SupplierResource\Pages\SupplierIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Supplier, SupplierIndexPage, SupplierFormPage, SupplierDetailPage>
 */
#[Icon('truck')]
class SupplierResource extends ModelResource
{
    protected string $model = Supplier::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Поставщики';
    }

    protected function pages(): array
    {
        return [
            SupplierIndexPage::class,
            SupplierFormPage::class,
            SupplierDetailPage::class,
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
