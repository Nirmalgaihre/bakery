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
            $table->string('name')->unique();
            $table->string('category');
            $table->decimal('purchase_cost', 12, 2);
            $table->decimal('selling_price', 12, 2);
            // Custom warehouse inventory units requested
            $table->enum('inventory_unit', ['kg', 'paau', 'bottle', 'cartoon', 'boxes']);
            $table->integer('initial_stock')->default(0);
            $table->integer('alert_stock_level')->default(5);
            $table->timestamps();
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