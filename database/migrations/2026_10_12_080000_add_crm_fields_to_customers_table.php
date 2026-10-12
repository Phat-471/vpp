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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('type', 30)->default('retail')->after('name'); // retail, wholesale, vip
            $table->decimal('credit_limit', 14, 2)->default(0)->after('debt_balance');
            $table->string('tax_id', 50)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['type', 'credit_limit', 'tax_id']);
        });
    }
};
