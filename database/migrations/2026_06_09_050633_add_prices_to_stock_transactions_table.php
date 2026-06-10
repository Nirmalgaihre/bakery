<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use Schema::table to alter an existing table structure
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->decimal('old_purchase_cost', 15, 2)->nullable()->after('type');
            $table->decimal('new_purchase_cost', 15, 2)->nullable()->after('old_purchase_cost');
            $table->decimal('old_selling_price', 15, 2)->nullable()->after('new_purchase_cost');
            $table->decimal('new_selling_price', 15, 2)->nullable()->after('old_selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'old_purchase_cost',
                'new_purchase_cost',
                'old_selling_price',
                'new_selling_price'
            ]);
        });
    }
};
