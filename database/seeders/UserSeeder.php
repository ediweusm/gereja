<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@sig.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password', // Casts hashed automatically in model
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        // 2. Staf Administrasi
        $staff = User::updateOrCreate(
            ['email' => 'staff@sig.test'],
            [
                'name' => 'Staf Administrasi',
                'password' => 'password',
                'is_active' => true,
            ]
        );
        $staff->assignRole('administrasi');

        // 3. Bendahara
        $treasurer = User::updateOrCreate(
            ['email' => 'bendahara@sig.test'],
            [
                'name' => 'Bendahara Gereja',
                'password' => 'password',
                'is_active' => true,
            ]
        );
        $treasurer->assignRole('bendahara');
    }
}
