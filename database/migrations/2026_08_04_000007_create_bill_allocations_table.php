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
        Schema::create('bill_allocations', function (Blueprint $table) {
            $table->id();
            $table->enum('bill_type', ['Purchase Invoice', 'Sales Invoice']);
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('payment_voucher_id')->nullable();
            $table->decimal('allocated_amount', 15, 2);
            $table->date('allocated_date');
            $table->decimal('adjustment_amount', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Partially Paid', 'Paid'])->default('Pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('payment_voucher_id')->references('id')->on('vouchers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('bill_type');
            $table->index('bill_id');
            $table->index('status');
            $table->index('allocated_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_allocations');
    }
};
