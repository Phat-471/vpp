<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('active_cashier_id')->nullable()->unique()->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('open')->index();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 16, 2);
            foreach (['cash_sales', 'transfer_sales', 'total_sales', 'expected_cash', 'counted_cash', 'difference'] as $field) {
                $table->decimal($field, 16, 2)->nullable();
            }
            $table->unsignedInteger('order_count')->nullable();
            $table->unsignedInteger('pending_transfer_count')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['cashier_id', 'opened_at']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts')->restrictOnDelete();
            $table->boolean('pos_outside_shift')->default(false)->index();
            $table->decimal('pos_shift_paid_at_close', 14, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pos_shift_id');
            $table->dropIndex(['pos_outside_shift']);
            $table->dropColumn(['pos_outside_shift', 'pos_shift_paid_at_close']);
        });
        Schema::dropIfExists('pos_shifts');
    }
};
