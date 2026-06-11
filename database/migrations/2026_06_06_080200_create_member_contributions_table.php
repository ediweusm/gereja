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
        Schema::create('member_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('contribution_type_id')->constrained('data_dictionaries')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_contributions');
    }
};
