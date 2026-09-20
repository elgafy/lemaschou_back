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
        Schema::table('occasion_special_items', function (Blueprint $table) {
            // Defaults to true so existing items stay available
            $table->boolean('active')->default(true);
            $table->text('unavailable_message_en')->nullable();
            $table->text('unavailable_message_ar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occasion_special_items', function (Blueprint $table) {
            $table->dropColumn(['active', 'unavailable_message_en', 'unavailable_message_ar']);
        });
    }
};
