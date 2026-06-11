<?php

namespace Database\Seeders;

use App\Models\MinistryRole;
use Illuminate\Database\Seeder;

class MinistryRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Pendeta',
            'Penatua',
            'Diaken',
            'Penginjil',
            'Pemandu Pujian',
            'Pemusik',
            'Penyanyi',
            'Pengerja Doa',
            'Majelis Gereja',
            'Penerima Tamu',
            'Kolektan',
            'Multi Media',
            'Guru Sekolah Minggu',
            'Dekorasi',
        ];

        foreach ($roles as $index => $role) {
            // Menggunakan updateOrCreate agar aman dari duplikasi
            // Index array dimulai dari 0, jadi ditambah 1 agar sort_order dimulai dari 1
            MinistryRole::updateOrCreate(
                ['name' => $role],
                ['sort_order' => $index + 1]
            );
        }
    }
}