<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Category;
use App\Models\Order;
use App\Models\Page as SitePage;
use App\Models\Product;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\Order\OrderResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\ProductResource\ProductResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

#[\MoonShine\MenuManager\Attributes\SkipMenu]
class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Панель управления';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        $productResource = app(ProductResource::class);
        $categoryResource = app(CategoryResource::class);
        $orderResource = app(OrderResource::class);
        $pageResource = app(PageResource::class);

        return [
            Grid::make([
                ValueMetric::make('Товары')
                    ->icon('shopping-bag')
                    ->iconColor(Color::BLUE)
                    ->value(static fn (): int => Product::query()->count())
                    ->columnSpan(3, 6),

                ValueMetric::make('Активные товары')
                    ->icon('check-circle')
                    ->iconColor(Color::GREEN)
                    ->value(static fn (): int => Product::query()->where('is_active', true)->count())
                    ->columnSpan(3, 6),

                ValueMetric::make('Категории')
                    ->icon('squares-2x2')
                    ->iconColor(Color::YELLOW)
                    ->value(static fn (): int => Category::query()->count())
                    ->columnSpan(3, 6),

                ValueMetric::make('Заказы')
                    ->icon('clipboard-document-list')
                    ->iconColor(Color::PURPLE)
                    ->value(static fn (): int => Order::query()->count())
                    ->columnSpan(3, 6),
            ]),

            Grid::make([
                Column::make([
                    Box::make('Быстрые действия', [
                        Div::make([
                            Badge::make('Админ-панель магазина', Color::BLUE),
                            Heading::make('Управляйте каталогом без лишних переходов', 2),
                        ])->class('space-y-3'),

                        Flex::make([
                            ActionButton::make('Создать товар', $productResource->getFormPageUrl())
                                ->primary()
                                ->icon('plus'),
                            ActionButton::make('Создать категорию', $categoryResource->getFormPageUrl())
                                ->secondary()
                                ->icon('folder-plus'),
                            ActionButton::make('Открыть сайт', route('home'))
                                ->info()
                                ->icon('globe-alt')
                                ->blank(),
                        ], justifyAlign: 'start')->class('gap-3'),
                    ])->icon('bolt'),
                ], 8, 12),

                Column::make([
                    Box::make('Навигация', [
                        ActionButton::make('Все товары', $productResource->getIndexPageUrl())
                            ->secondary()
                            ->icon('shopping-bag'),
                        ActionButton::make('Все категории', $categoryResource->getIndexPageUrl())
                            ->secondary()
                            ->icon('squares-2x2'),
                        ActionButton::make('Заказы', $orderResource->getIndexPageUrl())
                            ->secondary()
                            ->icon('clipboard-document-list'),
                        ActionButton::make('Страницы сайта', $pageResource->getIndexPageUrl())
                            ->secondary()
                            ->icon('document-text'),
                    ])->icon('map'),
                ], 4, 12),
            ]),

            Grid::make([
                ValueMetric::make('Страницы сайта')
                    ->icon('document-text')
                    ->iconColor(Color::BLUE)
                    ->value(static fn (): int => SitePage::query()->count())
                    ->columnSpan(4, 6),

                ValueMetric::make('Активные категории')
                    ->icon('folder')
                    ->iconColor(Color::GREEN)
                    ->value(static fn (): int => Category::query()->where('is_active', true)->count())
                    ->columnSpan(4, 6),

                ValueMetric::make('Товары в наличии')
                    ->icon('archive-box')
                    ->iconColor(Color::YELLOW)
                    ->value(static fn (): int => Product::query()->where('in_stock', true)->count())
                    ->columnSpan(4, 6),
            ]),
        ];
    }
}
