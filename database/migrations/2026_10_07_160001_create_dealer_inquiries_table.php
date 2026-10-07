<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealer_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 150);
            $table->string('contact_name', 100);
            $table->string('phone', 20)->index();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('business_type', 100)->nullable(); // Công ty, Trường học, Đại lý bán lẻ, v.v.
            $table->json('interested_categories')->nullable(); // Mảng các ngành hàng quan tâm
            $table->string('estimated_monthly_budget', 100)->nullable(); // Dưới 10tr, 10-30tr, 30-50tr, Trên 50tr
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending'); // pending, contacted, approved, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_inquiries');
    }
};
