<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\MoonShine\Resources\Order\Pages\OrderIndexPage;
use App\MoonShine\Resources\Order\Pages\OrderFormPage;
use App\MoonShine\Resources\Order\Pages\OrderDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Order, OrderIndexPage, OrderFormPage, OrderDetailPage>
 */
class OrderResource extends ModelResource
{
    protected string $model = Order::class;

    protected string $title = 'Заказы';

    protected string $column = 'name';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            OrderIndexPage::class,
            OrderFormPage::class,
            OrderDetailPage::class,
        ];
    }
}
