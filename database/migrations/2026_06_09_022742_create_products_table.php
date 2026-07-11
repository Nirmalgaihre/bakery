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
            $table->string('item_code')->unique(); // Added Item Code
            $table->string('name');
            $table->string('color')->nullable();   // Added Color
            $table->string('size')->nullable();    // Added Size
            
            // Using unsignedBigInteger for the category relationship
            $table->unsignedBigInteger('category_id'); 
            
            $table->decimal('purchase_cost', 12, 2);
            $table->decimal('selling_price', 12, 2);
            
            // Custom warehouse inventory units
            $table->enum('inventory_unit', ['kg', 'paau', 'bottle', 'cartoon', 'boxes']);
            $table->integer('initial_stock')->default(0);
            $table->integer('alert_stock_level')->default(5);
            $table->timestamps();

            // Optional: Add a foreign key constraint if you have a 'categories' table
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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