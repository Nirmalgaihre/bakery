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
        Schema::table('users', function (Blueprint $table) {
            // password कोलमको ठीक पछाडि 'role' कोलम थपिनेछ र यसको डिफल्ट भ्यालु 'operator' रहनेछ
            $table->string('role')->default('operator')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // यदि माइग्रेसन रोलब्याक गरियो भने यो कोलम हट्नेछ
            $table->dropColumn('role');
        });
    }
};