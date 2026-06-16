<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/xxxx_create_invoice_tables.php
public function up() {
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_no')->unique();
        $table->date('invoice_date');
        $table->foreignId('customer_id')->constrained();
        $table->decimal('grand_total', 15, 2);
        $table->timestamps();
    });

    Schema::create('invoice_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained();
        $table->decimal('qty', 10, 3);
        $table->string('unit');
        $table->decimal('price', 15, 2);
        $table->decimal('total', 15, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
