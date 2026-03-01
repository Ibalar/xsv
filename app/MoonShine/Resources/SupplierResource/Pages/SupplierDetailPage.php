<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SupplierResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<SupplierResource>
 */
final class SupplierDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Text::make('Slug', 'slug'),
            Textarea::make('Описание', 'description'),
            Switcher::make('Активен', 'is_active'),
        ];
    }
}
