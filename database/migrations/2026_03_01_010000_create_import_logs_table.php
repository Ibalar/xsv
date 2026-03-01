<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->enum('file_type', ['csv', 'xml']);
            $table->enum('status', ['pending', 'success', 'error'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('imported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
