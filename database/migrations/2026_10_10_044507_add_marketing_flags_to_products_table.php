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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_flash_sale')->default(false)->index()->after('is_active');
            $table->decimal('flash_sale_price', 12, 2)->nullable()->after('is_flash_sale');
            $table->boolean('is_best_seller')->default(false)->index()->after('flash_sale_price');
            $table->boolean('is_featured')->default(false)->index()->after('is_best_seller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_flash_sale', 'flash_sale_price', 'is_best_seller', 'is_featured']);
        });
    }
};
