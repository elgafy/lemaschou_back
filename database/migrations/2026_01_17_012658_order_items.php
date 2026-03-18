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
        Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->unique()->onDelete('cascade');
        $table->morphs('itemable'); // Polymorphic relation to various item types
        $table->decimal('price', 10, 2);
        $table->decimal('vat', 10, 2);
        $table->decimal('total_price', 10, 2);
        $table->unsignedInteger('quantity')->default(1);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
