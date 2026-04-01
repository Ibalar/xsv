<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Order\Pages;

use App\Models\Order;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Order\OrderResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Date;
use Throwable;


/**
 * @extends DetailPage<OrderResource>
 */
class OrderDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Select::make('Статус', 'status')
                ->options(Order::getStatusOptions())
                ->badge(fn($value) => match($value) {
                    Order::STATUS_NEW => 'blue',
                    Order::STATUS_PROCESSING => 'orange',
                    Order::STATUS_COMPLETED => 'green',
                    Order::STATUS_CANCELLED => 'red',
                    default => 'gray',
                }),
            Text::make('Имя', 'name'),
            Text::make('Телефон', 'phone'),
            Textarea::make('Комментарий', 'comment'),
            Json::make('Товары', 'products')
                ->fields([
                    Text::make('Название', 'name'),
                    Text::make('Цена', 'price'),
                    Text::make('Количество', 'quantity'),
                ]),
            Textarea::make('Заметки админа', 'admin_notes'),
            Switcher::make('Telegram отправлен', 'telegram_sent'),
            Date::make('Дата отправки', 'telegram_sent_at'),
            Date::make('Создан', 'created_at'),
            Date::make('Обновлён', 'updated_at'),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
