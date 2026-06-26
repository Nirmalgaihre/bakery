<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('cheques', function (Blueprint $table) {
        // कोलम नभए मात्र थप्ने लजिक
        if (!Schema::hasColumn('cheques', 'email_sent_at')) {
            $table->timestamp('email_sent_at')->nullable();
        }
    });
}

    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            $table->dropColumn('email_sent_at');
        });
    }
};