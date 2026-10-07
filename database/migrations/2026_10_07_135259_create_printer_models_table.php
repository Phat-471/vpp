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
        Schema::create('printer_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 60)->index();
            $table->string('model_name', 100)->index();
            $table->string('printer_type', 100)->default('Laser đen trắng');
            $table->string('compatible_cartridges', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printer_models');
    }
};
