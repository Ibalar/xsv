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
use MoonShine\MenuManager\MenuGroup;
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
            MenuGroup::make('Каталог')->setItems([
                MenuItem::make(CategoryResource::class),
                MenuItem::make(ProductResource::class),
                MenuItem::make(SupplierResource::class),
                MenuItem::make(CountryResource::class),
            ])
            ->icon('shopping-cart'),
            MenuGroup::make('Характеристики')->setItems([
                MenuItem::make(AttributeResource::class),
                MenuItem::make(AttributeValueResource::class),
            ])
            ->icon('adjustments-horizontal'),
            MenuItem::make(PageResource::class, 'Инфо. страницы')
            ->icon('document-text'),
            MenuItem::make(OrderResource::class, 'Заказы')
            ->icon('banknotes'),
            MenuItem::make(SiteSettingResource::class, 'Настройки сайта')
            ->icon('wrench-screwdriver'),
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

    protected function getFooterMenu(): array
    {
        return [
            'https://webart.by' => 'Разработка WebArt.by',
        ];
    }

    protected function getFooterCopyright(): string
    {
        return 'Все права защищены. www.xsv.by';
    }

}
