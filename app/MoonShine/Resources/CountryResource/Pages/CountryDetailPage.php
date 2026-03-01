<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CountryResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use App\MoonShine\Resources\CountryResource\CountryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<CountryResource>
 */
final class CountryDetailPage extends DetailPage
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
            Switcher::make('Активна', 'is_active'),
        ];
    }
}
