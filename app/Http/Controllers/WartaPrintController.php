<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ChurchProfile;
use Illuminate\Http\Request;

class WartaPrintController extends Controller
{
    public function printByRange(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $events = Event::whereBetween('event_date', [$startDate, $endDate])
            ->with([
                'rayon',
                'hostFamily.members.familyPosition',
                'assignments' => function ($query) {
                    $query->with(['ministryRole', 'member']);
                }
            ])
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789'
        ]);

        return view('reports.events-by-date', compact('events', 'startDate', 'endDate', 'profile'));
    }
}
