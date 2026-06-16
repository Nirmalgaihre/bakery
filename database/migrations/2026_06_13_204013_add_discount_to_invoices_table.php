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
    Schema::table('invoices', function (Blueprint $table) {
        // Add the discount column after subtotal
        if (!Schema::hasColumn('invoices', 'discount')) {
            $table->decimal('discount', 15, 2)->default(0)->after('subtotal');
        }
    });
}

public function down(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn('discount');
    });
}
};
