<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('gateway')->default('edfapay');

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');

            $table->string('status')->default('pending');

            // EdfaPay identifiers
            $table->string('gateway_order_id')->nullable()->index();
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->string('gateway_session_id')->nullable()->index();

            // Store the raw/extra gateway response when needed
            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
