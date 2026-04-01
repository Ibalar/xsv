<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'telegram_sent')) {
                $table->boolean('telegram_sent')->default(false)->after('user_agent');
            }
            if (!Schema::hasColumn('orders', 'telegram_sent_at')) {
                $table->timestamp('telegram_sent_at')->nullable()->after('telegram_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'telegram_sent_at')) {
                $table->dropColumn('telegram_sent_at');
            }
            if (Schema::hasColumn('orders', 'telegram_sent')) {
                $table->dropColumn('telegram_sent');
            }
        });
    }
};