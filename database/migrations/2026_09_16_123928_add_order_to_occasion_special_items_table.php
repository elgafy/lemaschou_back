<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('occasion_special_items', function (Blueprint $table) {
            $table->integer('order')->default(0);
        });

        // Seed the order of existing rows so manual reordering starts from a stable list
        DB::table('occasion_special_items')->update(['order' => DB::raw('id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occasion_special_items', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
