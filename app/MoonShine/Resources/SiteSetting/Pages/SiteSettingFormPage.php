<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SiteSetting\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;


/**
 * @extends FormPage<SiteSettingResource>
 */
class SiteSettingFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     * @throws Throwable
     */
    protected function fields(): iterable
    {
        $key = $this->getResource()->getItem()?->key;

        return match ($key) {
            'contacts' => $this->contactsFields(),
            'social_links' => $this->socialLinksFields(),
            'business_info' => $this->businessInfoFields(),
            default => $this->defaultFields(),
        };
    }

    /**
     * Поля для вкладки "Контакты"
     * @return list<ComponentContract|FieldContract>
     */
    protected function contactsFields(): array
    {
        return [
            Tabs::make([
                Tab::make('Телефоны', [
                    Box::make([
                        ID::make(),
                        Text::make('Ключ', 'key')
                            ->readonly()
                            ->sortable(),
                        Json::make('Телефоны', 'value')
                            ->fields([
                                Text::make('Номер телефона', 'number')
                                    ->required(),
                                Text::make('Метка (например: МТС, A1)', 'label')
                                    ->nullable(),
                            ])
                            ->removable()
                            ->creatable()
                            ->default([]),
                        Textarea::make('Описание', 'description')
                            ->readonly()
                            ->default('Список телефонов для отображения в шапке и футере'),
                    ]),
                ]),
                Tab::make('Email и Адрес', [
                    Box::make([
                        Text::make('Email', 'value.email')
                            ->type('email')
                            ->placeholder('xsv.by@yandex.by'),
                        Text::make('Адрес', 'value.address')
                            ->placeholder('Минская обл., Логойский р-н, д. Зелёный сад, ул. Подлесная, 20'),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * Поля для вкладки "Социальные сети и мессенджеры"
     * @return list<ComponentContract|FieldContract>
     */
    protected function socialLinksFields(): array
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Ключ', 'key')
                    ->readonly()
                    ->sortable(),
                Grid::make([
                    Column::make([
                        Text::make('Telegram', 'value.telegram')
                            ->placeholder('https://t.me/username'),
                    ], colSpan: 6),
                    Column::make([
                        Text::make('Viber', 'value.viber')
                            ->placeholder('viber://chat?number=%2B375296403709'),
                    ], colSpan: 6),
                    Column::make([
                        Text::make('WhatsApp', 'value.whatsapp')
                            ->placeholder('https://wa.me/375296403709'),
                    ], colSpan: 6),
                ]),
                Textarea::make('Описание', 'description')
                    ->readonly()
                    ->default('Ссылки на мессенджеры для отображения в шапке сайта'),
            ]),
        ];
    }

    /**
     * Поля для вкладки "Реквизиты компании"
     * @return list<ComponentContract|FieldContract>
     */
    protected function businessInfoFields(): array
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Ключ', 'key')
                    ->readonly()
                    ->sortable(),
                Grid::make([
                    Column::make([
                        Text::make('Название компании', 'value.company_name')
                            ->placeholder('ООО "Сказочный сад"'),
                        Text::make('УНП', 'value.unp')
                            ->placeholder('690876969'),
                    ], colSpan: 6),
                    Column::make([
                        Text::make('Расчётный счёт', 'value.bank_account')
                            ->placeholder('BY85 UNBS 3012 2578 3000 0000 0933'),
                        Text::make('Банк', 'value.bank_name')
                            ->placeholder('ЗАО БСБ Банк'),
                        Text::make('Адрес банка', 'value.bank_address')
                            ->placeholder('220004, г. Минск, пр. Победителей, 23, корп. 3'),
                        Text::make('БИК/Код банка', 'value.bank_code')
                            ->placeholder('UNBSBY2X'),
                    ], colSpan: 6),
                ]),
                Textarea::make('Дополнительная информация', 'value.additional_info')
                    ->placeholder('Свидетельство о регистрации выдано Логойским райисполком от 24.12.2025 г.')
                    ->rows(3),
                Textarea::make('Описание', 'description')
                    ->readonly()
                    ->default('Реквизиты компании для отображения в футере'),
            ]),
        ];
    }

    /**
     * Стандартные поля для остальных настроек
     * @return list<ComponentContract|FieldContract>
     */
    protected function defaultFields(): array
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Ключ', 'key')
                    ->required()
                    ->sortable(),
                Json::make('Значение', 'value')
                    ->fields([
                        Text::make('Значение'),
                    ])
                    ->keyValue(),
                Textarea::make('Описание', 'description')
                    ->nullable(),
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
