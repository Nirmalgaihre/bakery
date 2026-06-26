<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('inventory_adjustments', function (Blueprint $table) {
        // Change type to string (VARCHAR) to avoid ENUM truncation errors
        $table->string('type', 50)->change();
    });
}

public function down()
{
    Schema::table('inventory_adjustments', function (Blueprint $table) {
        // Revert back to ENUM if you ever need to (adjust values accordingly)
        $table->enum('type', ['sell', 'return', 'returned_defective'])->change();
    });
}
};
