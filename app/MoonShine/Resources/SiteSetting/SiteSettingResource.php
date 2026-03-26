<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SiteSetting;

use Illuminate\Database\Eloquent\Model;
use App\Models\SiteSetting;
use App\MoonShine\Resources\SiteSetting\Pages\SiteSettingIndexPage;
use App\MoonShine\Resources\SiteSetting\Pages\SiteSettingFormPage;
use App\MoonShine\Resources\SiteSetting\Pages\SiteSettingDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<SiteSetting, SiteSettingIndexPage, SiteSettingFormPage, SiteSettingDetailPage>
 */
class SiteSettingResource extends ModelResource
{
    protected string $model = SiteSetting::class;

    protected string $title = 'Настройки сайта';

    protected string $column = 'key';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SiteSettingIndexPage::class,
            SiteSettingFormPage::class,
            SiteSettingDetailPage::class,
        ];
    }
}
