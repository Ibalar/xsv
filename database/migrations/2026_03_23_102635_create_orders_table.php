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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json('products'); // массив товаров: id, name, quantity, price, image
            $table->string('name');
            $table->string('phone');
            $table->text('comment')->nullable();
            $table->boolean('agree')->default(false); // согласие с обработкой
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('telegram_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
