<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Family;
use App\Models\ChurchProfile;
use App\Models\MemberContribution;
use App\Models\MemberMutation;
use App\Models\MemberAssistance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;


class PastoralReportController extends Controller
{
    public function printBirthdays()
    {
        $daysOfThisWeek = [];
        $start = Carbon::now()->startOfWeek(); // Senin
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $daysOfThisWeek[] = [
                'month' => $date->month,
                'day' => $date->day,
            ];
        }

        $query = Member::query()
            ->with(['family.rayon'])
            ->where('is_deceased', false)
            ->whereNotNull('birth_date')
            ->where(function (Builder $q) use ($daysOfThisWeek) {
                foreach ($daysOfThisWeek as $day) {
                    $q->orWhere(function ($sub) use ($day) {
                        $sub->whereMonth('birth_date', $day['month'])
                            ->whereDay('birth_date', $day['day']);
                    });
                }
            });

        $cases = [];
        foreach ($daysOfThisWeek as $index => $day) {
            $cases[] = "WHEN MONTH(birth_date) = {$day['month']} AND DAY(birth_date) = {$day['day']} THEN {$index}";
        }
        $caseSql = "CASE " . implode(' ', $cases) . " ELSE 99 END";
        $query->orderByRaw($caseSql);

        $members = $query->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.birthdays', compact('members', 'profile'));
    }

    public function printUnderprivilegedFamilies()
    {
        $families = Family::query()
            ->with(['houseStatus', 'houseCategory', 'rayon', 'members.familyPosition'])
            ->needsAssistance()
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.underprivileged', compact('families', 'profile'));
    }

    public function printAdmissionsByRange(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $contributions = MemberContribution::query()
            ->with(['member.family.rayon', 'contributionType'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.admissions-by-range', compact('contributions', 'startDate', 'endDate', 'profile'));
    }

    public function printMutationsByRange(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $mutations = MemberMutation::with(['member', 'oldRayon', 'newRayon'])
            ->whereBetween('mutation_date', [$start, $end])
            ->orderBy('mutation_date', 'asc')
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.mutations-by-range', compact('mutations', 'startDate', 'endDate', 'profile'));
    }

    public function printMembersList(Request $request)
    {
        $statusId = $request->query('membership_status_id');
        $gender = $request->query('gender');

        $members = Member::with(['family', 'familyPosition', 'membershipStatus'])
            ->when($statusId, function ($query, $statusId) {
                return $query->where('membership_status_id', $statusId);
            })
            ->when($gender, function ($query, $gender) {
                return $query->where('gender', $gender);
            })
            ->orderBy('first_name', 'asc')
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.members-list', compact('members', 'statusId', 'gender', 'profile'));
    }

    public function printAssistancesByRange(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Mengganti 'transaction_date' menjadi 'created_at' agar sesuai dengan struktur database
        $assistances = MemberAssistance::with(['member', 'journal'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        $profile = ChurchProfile::first() ?? new ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.assistances-by-range', compact('assistances', 'startDate', 'endDate', 'profile'));
    }
}
