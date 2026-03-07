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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_email');
            $table->string('payment_status')->default('pending');
            $table->decimal('price', 10, 2);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('deposite', 10, 2)->nullable();
            $table->string('payment_processor')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();
            // Indexes for faster lookups
            $table->index(['user_id', 'user_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
