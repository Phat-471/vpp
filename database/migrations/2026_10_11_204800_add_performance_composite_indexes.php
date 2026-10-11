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
        // 1. Composite indexes for high-frequency product queries
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'category_id', 'id'], 'idx_products_active_cat_id');
            $table->index(['is_active', 'is_flash_sale', 'id'], 'idx_products_active_flash_id');
            $table->index(['is_active', 'is_best_seller', 'id'], 'idx_products_active_best_id');
            $table->index(['is_active', 'is_featured', 'id'], 'idx_products_active_feat_id');
        });

        // 2. Customer history lookup composite indexes for orders & tickets
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['customer_phone', 'id'], 'idx_orders_phone_id');
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
        });

        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->index(['customer_phone', 'id'], 'idx_tickets_phone_id');
            $table->index(['status', 'created_at'], 'idx_tickets_status_created');
        });

        // 3. Chat unread lookup composite index
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['chat_session_id', 'is_read', 'id'], 'idx_chat_messages_session_read_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_cat_id');
            $table->dropIndex('idx_products_active_flash_id');
            $table->dropIndex('idx_products_active_best_id');
            $table->dropIndex('idx_products_active_feat_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_phone_id');
            $table->dropIndex('idx_orders_status_created');
        });

        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_phone_id');
            $table->dropIndex('idx_tickets_status_created');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('idx_chat_messages_session_read_id');
        });
    }
};
