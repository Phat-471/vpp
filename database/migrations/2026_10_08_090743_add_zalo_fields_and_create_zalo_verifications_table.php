<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm các trường Zalo & xác thực vào bảng customers
        Schema::table('customers', function (Blueprint $table) {
            $table->string('zalo_id', 100)->nullable()->index()->after('remember_token');
            $table->string('zalo_name', 150)->nullable()->after('zalo_id');
            $table->string('zalo_avatar', 255)->nullable()->after('zalo_name');
            $table->timestamp('phone_verified_at')->nullable()->after('zalo_avatar');
        });

        // 2. Tạo bảng lưu phiên quét QR Zalo (Token, trạng thái, thời gian hết hạn)
        Schema::create('zalo_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->index();
            $table->string('status', 20)->default('pending')->index(); // pending, verified, expired
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('zalo_id', 100)->nullable()->index();
            $table->string('name', 150)->nullable();
            $table->string('phone', 20)->nullable()->index();
            $table->string('avatar', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zalo_verifications');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'zalo_id',
                'zalo_name',
                'zalo_avatar',
                'phone_verified_at',
            ]);
        });
    }
};
