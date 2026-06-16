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
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->string('item_name'); // Name of the ingredient/item
        $table->decimal('quantity', 10, 2); // Quantity purchased
        $table->string('unit'); // e.g., kg, liters, bags
        $table->decimal('price_per_unit', 10, 2);
        $table->decimal('total_amount', 10, 2);
        $table->string('supplier_name')->nullable();
        $table->date('purchase_date');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
