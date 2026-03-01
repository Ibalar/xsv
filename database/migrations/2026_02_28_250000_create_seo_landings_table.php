<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_landings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->json('filter_payload')->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_h1')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_landings');
    }
};
