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
    Schema::create('cheques', function (Blueprint $table) {
        $table->id();
        $table->string('cheque_no')->unique();
        $table->string('bank_name');
        $table->string('party_name');
        $table->decimal('amount', 15, 2);
        $table->date('issue_date_ad');
        $table->string('issue_date_bs');
        $table->date('maturity_date_ad');
        $table->string('maturity_date_bs');
        $table->enum('status', ['pending', 'due', 'cleared'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
