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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->date('transaction_date');
            $table->text('description');
            $table->string('reference_number', 100)->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->index('transaction_date', 'idx_journal_date');
            $table->index('transaction_number', 'idx_transaction_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
