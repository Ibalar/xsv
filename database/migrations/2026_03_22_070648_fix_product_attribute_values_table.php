<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Удаляем лишние уникальные ключи
            try {
                $table->dropUnique('unique_product_attribute_value');
            } catch (\Throwable $e) {}

            // Проверяем и создаём колонки, если их нет
            if (!Schema::hasColumn('product_attribute_values', 'product_id')) {
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('product_attribute_values', 'attribute_id')) {
                $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('product_attribute_values', 'attribute_value_id')) {
                $table->foreignId('attribute_value_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('product_attribute_values', 'value')) {
                $table->string('value')->nullable();
            }

            // Применяем корректный уникальный ключ
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'unique_product_attribute_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropUnique('unique_product_attribute_value');

            if (Schema::hasColumn('product_attribute_values', 'attribute_value_id')) {
                $table->dropForeign(['attribute_value_id']);
                $table->dropColumn('attribute_value_id');
            }
        });
    }
};
