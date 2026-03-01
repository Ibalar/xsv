<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CountryResource;

use App\Models\Country;
use App\MoonShine\Resources\CountryResource\Pages\CountryDetailPage;
use App\MoonShine\Resources\CountryResource\Pages\CountryFormPage;
use App\MoonShine\Resources\CountryResource\Pages\CountryIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Country, CountryIndexPage, CountryFormPage, CountryDetailPage>
 */
#[Icon('heroicons.globe-alt')]
class CountryResource extends ModelResource
{
    protected string $model = Country::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return 'Страны';
    }

    protected function pages(): array
    {
        return [
            CountryIndexPage::class,
            CountryFormPage::class,
            CountryDetailPage::class,
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
