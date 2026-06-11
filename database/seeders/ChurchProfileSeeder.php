<?php

namespace Database\Seeders;

use App\Models\ChurchProfile;
use Illuminate\Database\Seeder;

class ChurchProfileSeeder extends Seeder
{
    public function run(): void
    {
        ChurchProfile::updateOrCreate(
            ['id' => 1],
            [
                'gmit_name' => 'Majelis Sinode GMIT',
                'church_name' => 'Jemaat Sion Oepura',
                'address' => "Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara NTT",
                'phone' => '081123456789',
                'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
                'sekretaris' => 'Pnt. Markus, S.Sos',
                'bendahara' => 'Bertha S. Djahi',
                'logo_path' => "logos/01KTVRX53CEM59876DQMPS3XN4.png",
            ]
        );
    }
}
