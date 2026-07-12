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
    Schema::table('products', function (Blueprint $table) {
        // We use change() to modify the existing column type
        $table->decimal('initial_stock', 12, 3)->default(0.000)->change();
        $table->decimal('alert_stock_level', 12, 3)->default(5.000)->change();
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        // Revert back to integer
        $table->integer('initial_stock')->default(0)->change();
        $table->integer('alert_stock_level')->default(5)->change();
    });
}
};
