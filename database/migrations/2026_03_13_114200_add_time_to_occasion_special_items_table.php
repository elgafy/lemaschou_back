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
            $table->string('available_before_time')->nullable()->default('13')->after('reservation_availability_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occasion_special_items', function (Blueprint $table) {
            //
        });
    }
};
