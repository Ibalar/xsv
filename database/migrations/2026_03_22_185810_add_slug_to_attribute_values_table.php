<?php

use App\Models\AttributeValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('value');
        });

        // 🔥 Заполняем slug для существующих значений
        AttributeValue::query()->chunkById(100, function ($items) {
            foreach ($items as $item) {
                if (empty($item->slug)) {
                    $item->slug = Str::slug($item->value);
                    $item->save();
                }
            }
        });

        // Делаем уникальным (после заполнения!)
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
