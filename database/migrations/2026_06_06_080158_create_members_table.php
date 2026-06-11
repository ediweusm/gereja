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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
            
            // Biodata Dasar
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P']);
            
            // Status Sosial & Administratif
            $table->foreignId('family_position_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->foreignId('marital_status_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->foreignId('education_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->foreignId('occupation_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->decimal('income', 15, 2)->default(0.00);
            
            // Status Kegerejaan
            $table->foreignId('church_role_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            $table->foreignId('membership_status_id')->nullable()->constrained('data_dictionaries')->nullOnDelete();
            
            // Data Sakramen Baptis
            $table->boolean('status_baptis')->default(false);
            $table->date('baptism_date')->nullable();
            $table->string('baptism_church', 150)->nullable();
            $table->string('baptism_pastor', 100)->nullable();
            $table->string('baptism_witness_1', 100)->nullable();
            $table->string('baptism_witness_2', 100)->nullable();
            
            // Data Sakramen Sidi
            $table->date('sidi_date')->nullable();
            $table->string('sidi_church', 150)->nullable();
            $table->string('sidi_pastor', 100)->nullable();
            
            // Data Sakramen Pernikahan
            $table->date('marriage_date')->nullable();
            $table->string('marriage_church', 150)->nullable();
            $table->string('marriage_pastor', 100)->nullable();
            $table->string('marriage_witness_1', 100)->nullable();
            $table->string('marriage_witness_2', 100)->nullable();
            
            // Data Kematian
            $table->boolean('is_deceased')->default(false);
            $table->date('death_date')->nullable();
            
            $table->timestamps();
            
            // Indexing
            $table->index(['first_name', 'last_name'], 'idx_member_names');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
