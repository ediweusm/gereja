<?php

namespace Database\Seeders;

use App\Models\Rayon;
use Illuminate\Database\Seeder;

class RayonSeeder extends Seeder
{
    public function run(): void
    {
        $rayons = [
            ['name' => 'Rayon 1 (Matius)', 'description' => 'Mencakup area perumahan sektor utara gereja.'],
            ['name' => 'Rayon 2 (Markus)', 'description' => 'Mencakup area perumahan sektor timur gereja.'],
            ['name' => 'Rayon 3 (Lukas)', 'description' => 'Mencakup area perumahan sektor selatan gereja.'],
            ['name' => 'Rayon 4 (Yohanes)', 'description' => 'Mencakup area perumahan sektor barat gereja.'],
            ['name' => 'Rayon 5 (Petrus)', 'description' => 'Mencakup area perumahan sekitar pusat kota.'],
        ];

        foreach ($rayons as $rayon) {
            Rayon::updateOrCreate(
                ['name' => $rayon['name']],
                ['description' => $rayon['description']]
            );
        }
    }
}
