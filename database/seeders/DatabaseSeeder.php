<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key constraints during table cleanup
        Schema::disableForeignKeyConstraints();

        // Hapus data lama dengan urutan dependency yang benar
        \App\Models\MemberContribution::query()->delete();
        \App\Models\JournalItem::query()->delete();
        \App\Models\Journal::query()->delete();
        \App\Models\MemberMutation::query()->delete();
        \App\Models\Member::query()->delete();
        \App\Models\Family::query()->delete();
        \App\Models\Rayon::query()->delete();
        // \App\Models\User::query()->delete();
        \App\Models\ChurchProfile::query()->delete();

        // Enable foreign key constraints back
        Schema::enableForeignKeyConstraints();

        // Eksekusi seeders
        $this->call([
            RoleSeeder::class,
            AccountSeeder::class,
            DataDictionarySeeder::class,
            UserSeeder::class,
            RayonSeeder::class,
            FamilyAndMemberSeeder::class,
            JournalSeeder::class,
            ChurchProfileSeeder::class,
            MinistryRoleSeeder::class,
        ]);
    }
}
