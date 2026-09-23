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
        Schema::table('order_items', function (Blueprint $table) {
            // Snapshot of both languages at the time the order was placed.
            // The existing name/variation columns keep holding the English value.
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('variation_en')->nullable();
            $table->string('variation_ar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar', 'variation_en', 'variation_ar']);
        });
    }
};
