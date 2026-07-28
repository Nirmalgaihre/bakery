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
    Schema::table('purchases', function (Blueprint $table) {
        // String is common for formatted Bikram Sambat dates (e.g., '2083-04-12')
        $table->string('nepali_date')->nullable()->after('created_at'); 
    });
}

public function down(): void
{
    Schema::table('purchases', function (Blueprint $table) {
        $table->dropColumn('nepali_date');
    });
}
};
