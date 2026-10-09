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
        if (!Schema::hasTable('phone_otps')) {
            Schema::create('phone_otps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('phone', 20)->index();
                $table->string('otp_code', 10);
                $table->string('channel', 20)->default('zns'); // zns, sms, mock
                $table->string('action', 30)->default('verify'); // verify, login, reset_password
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->boolean('is_used')->default(false)->index();
                $table->timestamp('expires_at')->index();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_otps');
    }
};
