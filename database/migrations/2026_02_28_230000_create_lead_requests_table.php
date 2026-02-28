<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('name')->comment('Имя контактного лица');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('message')->nullable()->comment('Сообщение от клиента');
            $table->text('notes')->nullable()->comment('Заметки менеджера');

            $table->string('status')->default('new')->comment('Статус заявки');
            $table->unsignedInteger('quantity')->nullable()->comment('Запрашиваемое количество товара');

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('product_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_requests');
    }
};
