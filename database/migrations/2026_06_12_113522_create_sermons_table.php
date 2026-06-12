<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('preacher')->nullable(); // Nama pembicara/pendeta
            $table->string('passage')->nullable(); // Nats/Ayat Alkitab
            $table->text('content_summary')->nullable(); // Ringkasan
            $table->string('video_url')->nullable(); // Link YouTube
            $table->string('audio_url')->nullable(); // Link Spotify/Audio
            $table->date('sermon_date'); // Tanggal ibadah/khotbah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sermons');
    }
};