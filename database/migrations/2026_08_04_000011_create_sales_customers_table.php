<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_code')->unique();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('gstin')->nullable();
            $table->string('credit_limit')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('balance_type', ['Debit', 'Credit'])->default('Debit');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->index('status');
            $table->index('customer_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_customers');
    }
};
