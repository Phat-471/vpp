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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // PT260001 (Thu) hoặc PC260001 (Chi)
            $table->string('type', 20)->index(); // 'receipt' (Thu), 'payment' (Chi)
            $table->string('category', 50)->index(); 
            $table->decimal('amount', 14, 2);
            $table->string('payment_method', 30)->default('cash'); // 'cash', 'bank_transfer'
            
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('repair_ticket_id')->nullable()->constrained('repair_tickets')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 20)->default('completed'); // 'completed', 'cancelled'
            $table->dateTime('performed_at')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
