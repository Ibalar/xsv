<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CategoryResource\Pages;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Number;
use Leeto\MoonShineTree\View\Components\TreeComponent;

/**
 * @extends IndexPage<CategoryResource>
 */
class CategoryIndexPage extends IndexPage
{
    protected function mainLayer(): array
    {
        return [
            TreeComponent::make($this->getResource()),
        ];
    }



}
