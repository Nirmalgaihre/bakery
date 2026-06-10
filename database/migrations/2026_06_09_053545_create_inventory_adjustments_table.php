<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Always positive for the adjusted count
            
            // The exact movement types you requested
            $table->enum('type', [
                'sell', 
                'expired', 
                'damaged', 
                'returned_defective', 
                'internal_use', 
                'wastage'
            ]);
            
            $table->decimal('unit_cost', 15, 2)->nullable(); // Financial valuation tracking
            $table->string('reference_note')->nullable(); // e.g., "Staff tea time break", "Invoice #10"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};