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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('sku', 60)->unique();
            $table->string('barcode', 60)->nullable()->index();
            $table->string('name', 255)->index();
            $table->string('slug', 255)->unique();
            $table->decimal('cost_price', 14, 2)->default(0);
            $table->decimal('retail_price', 14, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->string('base_unit', 30)->default('Cái');
            $table->string('image_path', 255)->nullable();
            $table->boolean('has_custom_image')->default(false)->index();
            $table->boolean('is_service_part')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
