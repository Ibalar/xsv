<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SiteSetting\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use MoonShine\Support\ListOf;
use Throwable;


/**
 * @extends IndexPage<SiteSettingResource>
 */
class SiteSettingIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Ключ', 'key')
                ->sortable()
                ->badge('info'),

            Text::make('Описание', 'description')
                ->changePreview(static function (mixed $value): string {
                    return filled($value) ? (string) $value : 'Без описания';
                }),

            Preview::make('Значение', 'value')
                ->changePreview(static function (mixed $value): string {
                    $data = \App\Models\SiteSetting::normalizeArray($value);

                    if ($data === []) {
                        return 'Не задано';
                    }

                    $preview = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    if ($preview === false) {
                        return 'Не удалось отобразить значение';
                    }

                    return mb_strlen($preview) > 140
                        ? mb_substr($preview, 0, 140) . '...'
                        : $preview;
                }),

            Date::make('Обновлено', 'updated_at')
                ->format('d.m.Y H:i')
                ->sortable(),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component->columnSelection();
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
