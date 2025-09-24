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
        Schema::create('cart_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->nullable()->constrained()->onDelete('cascade'); // attribute specific
            $table->unsignedInteger('quantity');
            $table->timestamp('reserved_until');
            $table->timestamps();

            $table->index(['product_id', 'product_attribute_id', 'reserved_until'], 'cart_reservations_prod_attr_reserved_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_reservations');
    }
};
