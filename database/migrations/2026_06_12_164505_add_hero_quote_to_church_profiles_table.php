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
        Schema::table('church_profiles', function (Blueprint $table) {
            // Menggunakan tipe data text agar bisa menampung kalimat panjang
            $table->text('hero_quote')->nullable()->after('hero_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('church_profiles', function (Blueprint $table) {
            $table->dropColumn('hero_quote');
        });
    }
};
