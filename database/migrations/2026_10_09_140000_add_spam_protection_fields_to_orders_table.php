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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('notes')->index();
            }
            if (!Schema::hasColumn('orders', 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false)->after('ip_address')->index();
            }
            if (!Schema::hasColumn('orders', 'suspicious_reason')) {
                $table->string('suspicious_reason', 255)->nullable()->after('is_suspicious');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'is_suspicious', 'suspicious_reason']);
        });
    }
};
