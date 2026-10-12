<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicate = DB::table('payment_transactions')
            ->select('gateway', 'transaction_id')
            ->whereNotNull('transaction_id')
            ->groupBy('gateway', 'transaction_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException('Cannot add payment transaction unique index: duplicate gateway/transaction_id records exist. Review them manually first.');
        }

        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->unique(['gateway', 'transaction_id'], 'payment_transactions_gateway_tx_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->dropUnique('payment_transactions_gateway_tx_unique');
        });
    }
};
