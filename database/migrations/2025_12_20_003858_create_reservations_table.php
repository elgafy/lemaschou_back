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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('hold');
            $table->date('date');
            $table->string('time');
            $table->unsignedInteger('guests_count');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile');
            $table->text('special_request')->nullable();
            $table->boolean('occasion')->nullable();
            $table->string('occasion_type')->nullable();
            $table->json('occasion_items')->nullable();
            $table->boolean('allergic')->nullable();
            $table->json('food_allergies')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('payment_terms_accepted')->default(false);
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('sevenrooms_reservation_id')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
