<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['product_id', 'attribute_id']);

            // Add a new unique constraint to prevent duplicate value assignments
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'unique_product_attribute_value');
        });
    }

    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('unique_product_attribute_value');

            // Restore the old unique constraint
            $table->unique(['product_id', 'attribute_id']);
        });
    }
};
