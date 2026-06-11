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
        Schema::create('member_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->enum('mutation_type', ['Atestasi Masuk', 'Atestasi Keluar', 'Pindah Rayon', 'Titipan', 'Lainnya']);
            $table->date('mutation_date');
            $table->string('origin_church', 150)->nullable();
            $table->string('destination_church', 150)->nullable();
            $table->foreignId('old_rayon_id')->nullable()->constrained('rayons')->nullOnDelete();
            $table->foreignId('new_rayon_id')->nullable()->constrained('rayons')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->string('document_number', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_mutations');
    }
};
