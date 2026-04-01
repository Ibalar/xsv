<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Order\Pages;

use App\Models\Order;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Order\OrderResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;


/**
 * @extends FormPage<OrderResource>
 */
class OrderFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Информация о заказе', [
                ID::make(),
                Select::make('Статус', 'status')
                    ->options(Order::getStatusOptions())
                    ->default(Order::STATUS_NEW),
            ]),
            Box::make('Данные клиента', [
                Text::make('Имя', 'name'),
                Text::make('Телефон', 'phone'),
                Textarea::make('Комментарий клиента', 'comment'),
            ]),
            Box::make('Товары', [
                Json::make('Товары', 'products')
                    ->fields([
                        Text::make('Название', 'name'),
                        Text::make('Цена', 'price'),
                        Text::make('Количество', 'quantity'),
                    ])
                    ->hideOnIndex(),
            ]),
            Box::make('Системная информация', [
                Textarea::make('Заметки админа', 'admin_notes'),
                Switcher::make('Telegram отправлен', 'telegram_sent')->readonly(),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
