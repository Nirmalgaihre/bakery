<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Customers table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'previous_due')) {
                $table->decimal('previous_due', 12, 2)->default(0);
            }
        });

        // 2. Update Invoices table safely
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('Paid');
            }
            if (!Schema::hasColumn('invoices', 'grand_total')) {
                $table->decimal('grand_total', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'taxable_amount')) {
                $table->decimal('taxable_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'vat_amount')) {
                $table->decimal('vat_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'remarks')) {
                $table->text('remarks')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'nepali_date')) {
                $table->string('nepali_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Add logic here if you ever need to rollback
    }
};