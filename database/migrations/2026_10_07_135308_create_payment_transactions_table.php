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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 30)->default('sepay')->index();
            $table->string('transaction_id', 100)->nullable()->index();
            $table->string('reference_code', 50)->index();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('repair_ticket_id')->nullable()->constrained('repair_tickets')->nullOnDelete();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('account_number', 50)->nullable();
            $table->string('bank_brand_name', 50)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('transaction_time')->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('status', 30)->default('processed')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
