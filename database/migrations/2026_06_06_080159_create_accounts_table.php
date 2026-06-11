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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->enum('type', ['Asset', 'Liability', 'Net Asset', 'Revenue', 'Expense']);
            $table->enum('restriction_type', ['Tidak Terikat', 'Terikat Temporer', 'Terikat Permanen'])->default('Tidak Terikat');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code', 'idx_account_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
