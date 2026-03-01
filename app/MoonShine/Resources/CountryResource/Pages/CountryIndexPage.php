<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CountryResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\CountryResource\CountryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<CountryResource>
 */
final class CountryIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Название', 'name')
                ->sortable(),

            Text::make('Slug', 'slug')
                ->sortable(),

            Switcher::make('Активна', 'is_active')
                ->sortable(),
        ];
    }
}
