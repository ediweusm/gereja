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
        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index(['journal_id', 'account_id'], 'idx_journal_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_items');
    }
};
