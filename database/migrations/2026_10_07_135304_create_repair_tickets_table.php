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
        Schema::create('repair_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ticket_code', 30)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20)->index();
            $table->char('phone_last4', 4)->index();
            $table->foreignId('printer_model_id')->nullable()->constrained('printer_models')->nullOnDelete();
            $table->string('device_name', 150);
            $table->string('serial_number', 100)->nullable();
            $table->string('accessories', 255)->nullable();
            $table->text('issue_description');
            $table->text('technician_diagnosis')->nullable();
            $table->string('intake_flow', 30)->default('quote_immediate');
            $table->string('status', 30)->default('received')->index();
            $table->decimal('labor_fee', 14, 2)->default(0);
            $table->decimal('parts_total', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->string('payment_method', 30)->default('cash');
            $table->dateTime('promised_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_tickets');
    }
};
