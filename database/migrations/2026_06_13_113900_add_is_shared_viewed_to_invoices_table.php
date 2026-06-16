<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safe structural check preventing duplicate column errors
        if (!Schema::hasColumn('invoices', 'is_shared_viewed')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->boolean('is_shared_viewed')->default(false)->after('grand_total');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'is_shared_viewed')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('is_shared_viewed');
            });
        }
    }
};