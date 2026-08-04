<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('item_code');
            $table->decimal('quantity', 15, 4);
            $table->string('unit');
            $table->decimal('rate', 15, 2);
            $table->decimal('line_amount', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sales_items')->onDelete('set null');
            $table->index('item_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
