<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->comment('Артикул для импорта/экспорта');

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->string('image')->nullable();
            $table->json('gallery')->nullable();

            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('old_price', 12, 2)->nullable()->comment('Зачёркнутая цена');
            $table->decimal('wholesale_price', 12, 2)->nullable()->comment('Оптовая цена');
            $table->unsignedInteger('wholesale_min_quantity')->nullable()->comment('Минимальное количество для оптовой цены');

            $table->unsignedInteger('stock')->default(0);
            $table->boolean('in_stock')->default(true);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_bestseller')->default(false);

            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('views')->default(0)->comment('Счётчик просмотров');

            $table->string('seo_title')->nullable();
            $table->string('seo_h1')->nullable();
            $table->text('seo_description')->nullable();

            $table->json('attributes')->nullable()->comment('Дополнительные характеристики товара');

            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('is_new');
            $table->index('is_bestseller');
            $table->index('price');
            $table->index('sort_order');
            $table->index('views');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
