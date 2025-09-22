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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_token', 100)->nullable();

            $table->timestamps();

            $table->index('user_id'); 
            $table->index('guest_token');
            $table->index('created_at');

            $table->unique(['user_id', 'guest_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
