<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's site settings.
     */
    public function run(): void
    {
        // Контакты: телефоны, email, адрес
        SiteSetting::set(
            'contacts',
            [
                'value' => [
                    ['number' => '+375 (29) 640-37-09', 'label' => 'МТС'],
                    ['number' => '+375 (44) 591-33-35', 'label' => 'A1'],
                    ['number' => '+375 (29) 591-33-72', 'label' => 'МТС'],
                ],
                'email' => 'xsv.by@yandex.by',
                'address' => "Минская обл., Логойский р-н\nд. Зелёный сад, ул. Подлесная, 20",
            ],
            'Контактная информация: телефоны, email, адрес'
        );

        // Социальные сети и мессенджеры
        SiteSetting::set(
            'social_links',
            [
                'telegram' => '#',
                'viber' => 'viber://chat?number=%2B375296403709',
                'whatsapp' => '#',
            ],
            'Ссылки на мессенджеры для отображения в шапке сайта'
        );

        // Реквизиты компании
        SiteSetting::set(
            'business_info',
            [
                'company_name' => 'ООО "Сказочный сад"',
                'unp' => '690876969',
                'additional_info' => 'Свидетельство о регистрации выдано Логойским райисполком от 24.12.2025 г.',
                'bank_account' => 'BY85 UNBS 3012 2578 3000 0000 0933',
                'bank_name' => 'ЗАО БСБ Банк',
                'bank_address' => '220004, г. Минск, пр. Победителей, 23, корп. 3',
                'bank_code' => 'UNBSBY2X',
            ],
            'Реквизиты компании для отображения в футере'
        );
    }
}
