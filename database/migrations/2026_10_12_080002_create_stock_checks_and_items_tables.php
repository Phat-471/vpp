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
        Schema::create('stock_checks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // KK260001
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index(); // 'draft', 'completed', 'cancelled'
            $table->integer('total_items')->default(0);
            $table->integer('matched_items')->default(0);
            $table->integer('difference_items')->default(0);
            $table->integer('total_system_qty')->default(0);
            $table->integer('total_actual_qty')->default(0);
            $table->decimal('total_difference_value', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_check_id')->constrained('stock_checks')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('product_name', 255);
            $table->string('unit_name', 50)->default('Cái');
            $table->integer('system_quantity')->default(0);
            $table->integer('actual_quantity')->default(0);
            $table->integer('difference')->default(0); // actual - system
            $table->decimal('cost_price', 14, 2)->default(0);
            $table->decimal('difference_value', 14, 2)->default(0); // difference * cost_price
            $table->string('reason', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_check_items');
        Schema::dropIfExists('stock_checks');
    }
};
