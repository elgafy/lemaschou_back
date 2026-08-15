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

            $table->foreignId('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('payment_processor')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('status');

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
