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
        Schema::create('ad_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ad_id")->constrained()->cascadeOnDelete();
            $table->enum('page',['home','menu','venue']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_pages');
    }
};
