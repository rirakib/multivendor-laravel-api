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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_token', 100)->nullable();

            $table->string('action')->index();
            $table->string('entity_type')->nullable()->index();
            $table->unsignedBigInteger('entity_id')->nullable()->index();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['guest_token', 'action']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
