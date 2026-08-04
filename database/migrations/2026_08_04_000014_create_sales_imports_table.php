<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_imports', function (Blueprint $table) {
            $table->id();
            $table->string('import_name');
            $table->string('file_name')->nullable();
            $table->enum('import_type', ['Tally', 'CSV', 'Excel', 'Manual'])->default('Tally');
            $table->integer('total_records')->default(0);
            $table->integer('successfully_imported')->default(0);
            $table->integer('failed_records')->default(0);
            $table->json('error_logs')->nullable();
            $table->enum('status', ['Pending', 'Processing', 'Completed', 'Failed'])->default('Pending');
            $table->dateTime('import_date');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->index('import_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_imports');
    }
};
