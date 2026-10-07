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
        // 1. Settings Table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->longText('value')->nullable();
                $table->string('group', 50)->default('general')->index();
                $table->string('type', 30)->default('text');
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 2. Chat Sessions & Messages
        if (!Schema::hasTable('chat_sessions')) {
            Schema::create('chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_token', 64)->unique()->index();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('customer_name', 100)->default('Khách truy cập');
                $table->string('customer_phone', 20)->nullable();
                $table->string('status', 30)->default('active'); // active, closed
                $table->unsignedInteger('unread_admin')->default(0);
                $table->unsignedInteger('unread_customer')->default(0);
                $table->text('last_message')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_session_id')->constrained('chat_sessions')->cascadeOnDelete();
                $table->string('sender_type', 20)->default('customer'); // customer, admin
                $table->string('sender_name', 100)->default('Khách');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // 3. Dynamic CMS Pages
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('slug', 255)->unique();
                $table->text('summary')->nullable();
                $table->longText('content');
                $table->string('category', 50)->default('policy'); // policy, about, guide, announcement
                $table->string('position', 50)->default('footer_col1'); // footer_col1, footer_col2, header, hidden
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('meta_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Add VAT invoice fields to orders table if not present
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_vat_invoice')) {
                $table->boolean('is_vat_invoice')->default(false)->after('channel');
            }
            if (!Schema::hasColumn('orders', 'company_name')) {
                $table->string('company_name', 255)->nullable()->after('is_vat_invoice');
            }
            if (!Schema::hasColumn('orders', 'company_tax_id')) {
                $table->string('company_tax_id', 50)->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('orders', 'company_address')) {
                $table->string('company_address', 255)->nullable()->after('company_tax_id');
            }
            if (!Schema::hasColumn('orders', 'invoice_email')) {
                $table->string('invoice_email', 100)->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 14, 2)->default(0)->after('grand_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_vat_invoice',
                'company_name',
                'company_tax_id',
                'company_address',
                'invoice_email',
                'shipping_fee',
            ]);
        });
        Schema::dropIfExists('pages');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('settings');
    }
};
