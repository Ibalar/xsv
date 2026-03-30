<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\Order\OrderResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\ProductAttributeValue\ProductAttributeValueResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\SkyPalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = SkyPalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuItem::make(CategoryResource::class),
            MenuItem::make(SupplierResource::class),
            MenuItem::make(CountryResource::class),
            MenuItem::make(ProductResource::class),
            MenuItem::make(AttributeResource::class),
            MenuItem::make(AttributeValueResource::class),
            MenuItem::make(OrderResource::class, 'Заказы'),
            MenuItem::make(PageResource::class, 'Страницы сайта'),
            MenuItem::make(SiteSettingResource::class, 'Настройки сайта'),
            MenuItem::make(static fn (): string => route('home'), 'Открыть сайт', 'globe-alt', true),
            ...parent::menu(),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#000000');
    }
}
