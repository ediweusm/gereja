<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\ChurchProfile;
use Illuminate\Http\Request;

class FamilyPrintController extends Controller
{
    public function print(Family $family)
    {
        // Eager load relations to prevent N+1 queries
        $family->load([
            'rayon',
            'houseCategory',
            'houseStatus',
            'members' => function ($query) {
                // Ensure members are sorted by their family position (head of family first, etc.)
                // Or just load relationships
                $query->with([
                    'familyPosition',
                    'maritalStatus',
                    'education',
                    'occupation',
                    'churchRole',
                    'membershipStatus'
                ]);
            }
        ]);

        // Load Church Profile for letterhead kop
        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789'
        ]);

        return view('reports.kartu-keluarga', compact('family', 'profile'));
    }
}
