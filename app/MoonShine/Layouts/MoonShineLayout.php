<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\Models\Category;
use App\MoonShine\Resources\AttributeResource\AttributeResource;
use App\MoonShine\Resources\AttributeValueResource\AttributeValueResource;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\Order\OrderResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use Illuminate\Support\Collection;
use MoonShine\AssetManager\InlineCss;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\SkyPalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Contracts\Core\ResourceContract;
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
            InlineCss::make(<<<'CSS'
                .moonshine-products-menu > .menu-submenu {
                    max-height: min(58vh, 620px);
                    overflow-y: auto;
                    overscroll-behavior: contain;
                    scrollbar-width: thin;
                }

                .moonshine-products-menu .menu-submenu .menu-submenu {
                    max-height: none;
                    overflow: visible;
                }
            CSS),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Каталог')->setItems([
                MenuItem::make(CategoryResource::class),
                MenuItem::make(SupplierResource::class),
                MenuItem::make(CountryResource::class),
            ])
                ->icon('shopping-cart'),
            $this->makeProductsMenu(),
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

    private function makeProductsMenu(): MenuGroup
    {
        $productResource = moonshine()->getResources()->findByClass(ProductResource::class);
        $productUrl = $productResource instanceof ResourceContract
            ? $productResource->getUrl()
            : '/';
        $categoriesByParent = Category::query()
            ->active()
            ->ordered()
            ->get()
            ->groupBy('parent_id');

        return MenuGroup::make('Товары')
            ->setItems([
                MenuItem::make($productUrl, 'Все товары')
                    ->icon('squares-2x2')
                    ->whenActive(
                        static fn (): bool => request()->url() === $productUrl && ! request()->has('category_id')
                    ),
                ...$this->makeProductCategoryMenuItems($categoriesByParent, $productUrl),
            ])
            ->icon('shopping-bag')
            ->customAttributes([
                'class' => 'moonshine-products-menu',
            ]);
    }

    /**
     * @param  Collection<int|string, \Illuminate\Database\Eloquent\Collection<int, Category>>  $categoriesByParent
     * @return list<MenuGroup|MenuItem>
     */
    private function makeProductCategoryMenuItems($categoriesByParent, string $productUrl, ?int $parentId = null): array
    {
        return $categoriesByParent
            ->get($parentId ?? '', collect())
            ->map(function (Category $category) use ($categoriesByParent, $productUrl): MenuGroup|MenuItem {
                $url = $productUrl.'?'.http_build_query(['category_id' => $category->id]);
                $children = $this->makeProductCategoryMenuItems($categoriesByParent, $productUrl, $category->id);

                $item = MenuItem::make($url, $category->name)
                    ->whenActive(
                        static fn (): bool => request()->integer('category_id') === $category->id
                    );

                if ($children === []) {
                    return $item;
                }

                return MenuGroup::make($category->name)
                    ->setItems([
                        $item,
                        ...$children,
                    ]);
            })
            ->values()
            ->all();
    }

    /**
     * @param  ColorManager  $colorManager
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
