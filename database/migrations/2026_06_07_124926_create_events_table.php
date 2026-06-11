<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('theme')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->string('event_type'); // Contoh: Ibadah Raya, Persekutuan Wilayah
            $table->string('mode')->default('onsite'); // onsite, online, hybrid
            
            // Relasi ke lokasi/tempat
            $table->foreignId('rayon_id')->nullable()->constrained('rayons')->nullOnDelete();
            $table->foreignId('host_family_id')->nullable()->constrained('families')->nullOnDelete();
            $table->string('location_notes')->nullable(); // Untuk teks bebas seperti "Ruang Aula Lt. 2"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};