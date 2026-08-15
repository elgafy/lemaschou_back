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
        Schema::create('occasion_special_items', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('description_en');
            $table->string('description_ar');
            $table->unsignedInteger('price');
            $table->boolean('has_variations');
            $table->unsignedInteger('reservation_availability_period')->default(0);
            $table->string('available_before_time')->nullable()->default('13');
            $table->foreignId('category')->constrained('occasion_special_items_categories', 'id');
            $table->string('image');
            $table->json('options');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occasion_special_items');
    }
};
