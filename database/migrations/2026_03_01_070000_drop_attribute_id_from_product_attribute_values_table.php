<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing unique constraint that includes attribute_id
        try {
            Schema::table('product_attribute_values', function (Blueprint $table) {
                $table->dropUnique('unique_product_attribute_value');
            });
        } catch (\Throwable $e) {
            // Constraint might not exist, continue
        }

        // Drop the attribute_id column and its foreign key
        try {
            Schema::table('product_attribute_values', function (Blueprint $table) {
                $table->dropForeign(['attribute_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key might not exist, continue
        }

        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Drop column if it still exists
            if (Schema::hasColumn('product_attribute_values', 'attribute_id')) {
                $table->dropColumn('attribute_id');
            }
        });

        // Add a new unique constraint on product_id and attribute_value_id
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->unique(['product_id', 'attribute_value_id'], 'unique_product_attribute_value');
        });
    }

    public function down(): void
    {
        // Drop the new unique constraint
        try {
            Schema::table('product_attribute_values', function (Blueprint $table) {
                $table->dropUnique('unique_product_attribute_value');
            });
        } catch (\Throwable $e) {
            // Constraint might not exist, continue
        }

        Schema::table('product_attribute_values', function (Blueprint $table) {
            // Restore the attribute_id column with its foreign key
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();

            // Restore the old unique constraint
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'unique_product_attribute_value');
        });
    }
};
