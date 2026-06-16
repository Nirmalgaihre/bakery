<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('invoices', function (Blueprint $table) {
        if (!Schema::hasColumn('invoices', 'invoice_number')) $table->string('invoice_number')->nullable();
        if (!Schema::hasColumn('invoices', 'nepali_date')) $table->string('nepali_date')->nullable();
        if (!Schema::hasColumn('invoices', 'subtotal')) $table->decimal('subtotal', 15, 2)->default(0);
        if (!Schema::hasColumn('invoices', 'taxable_amount')) $table->decimal('taxable_amount', 15, 2)->default(0);
        if (!Schema::hasColumn('invoices', 'vat_amount')) $table->decimal('vat_amount', 15, 2)->default(0);
        if (!Schema::hasColumn('invoices', 'grand_total')) $table->decimal('grand_total', 15, 2)->default(0);
        if (!Schema::hasColumn('invoices', 'paid_amount')) $table->decimal('paid_amount', 15, 2)->default(0);
        if (!Schema::hasColumn('invoices', 'payment_method')) $table->string('payment_method')->nullable();
        if (!Schema::hasColumn('invoices', 'status')) $table->string('status')->default('Unpaid');
        if (!Schema::hasColumn('invoices', 'remarks')) $table->text('remarks')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
