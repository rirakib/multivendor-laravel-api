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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('brand_id')->nullable()->constrained('brands');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->foreignId('thumbnail_id')->nullable()->constrained('product_images')->nullOnDelete()
                ->index();
            $table->timestamps();

            $table->index(['category_id', 'status'], 'idx_category_status');
            $table->index('vendor_id', 'idx_vendor');
            $table->index('stock_quantity', 'idx_stock_quantity');
            $table->index('thumbnail_id', 'idx_thumbnail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
