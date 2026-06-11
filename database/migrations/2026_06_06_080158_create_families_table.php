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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('family_number', 50)->unique();
            $table->foreignId('rayon_id')->nullable()->constrained('rayons')->nullOnDelete();
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->foreignId('house_category_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->foreignId('house_status_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('family_number', 'idx_family_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
