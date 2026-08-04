<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_code')->unique();
            $table->text('description')->nullable();
            $table->string('unit'); // kg, pcs, roll, etc
            $table->decimal('opening_price', 15, 2);
            $table->decimal('current_price', 15, 2);
            $table->decimal('quantity_in_hand', 15, 4)->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->index('status');
            $table->index('item_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};
