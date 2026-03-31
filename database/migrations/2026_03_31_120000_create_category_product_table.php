<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'product_id']);
            $table->index('product_id');
        });

        DB::table('products')
            ->select(['category_id', 'id'])
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->chunk(500, function ($products): void {
                $now = now();
                $rows = [];

                foreach ($products as $product) {
                    $rows[] = [
                        'category_id' => $product->category_id,
                        'product_id' => $product->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows !== []) {
                    DB::table('category_product')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
