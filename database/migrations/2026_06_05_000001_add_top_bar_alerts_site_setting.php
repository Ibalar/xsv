<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('site_settings')->where('key', 'top_bar_alerts')->exists()) {
            DB::table('site_settings')->insert([
                'key' => 'top_bar_alerts',
                'value' => json_encode([], JSON_THROW_ON_ERROR),
                'description' => 'Тексты для слайдера в верхней alert-плашке сайта',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->where('key', 'top_bar_alerts')
            ->delete();
    }
};
