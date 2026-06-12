<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\MemberContribution;
use App\Models\MemberAssistance;
use Illuminate\Http\Request;

class JournalPrintController extends Controller
{
    public function print(Journal $journal)
    {
        // Eager load relations
        $journal->load(['items.account', 'createdBy']);

        return view('reports.journal-voucher', compact('journal'));
    }

    public function printKwitansi(Journal $journal)
    {
        // Eager load relations including contributions and member details
        $journal->load(['items.account', 'createdBy', 'contributions.member']);

        return view('reports.kwitansi', compact('journal'));
    }

    public function printContributionReceipt(MemberContribution $contribution)
    {
        // Load relationships
        $contribution->load(['member', 'contributionType', 'journal']);

        $profile = \App\Models\ChurchProfile::first() ?? new \App\Models\ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789'
        ]);

        return view('reports.contribution-receipt', compact('contribution', 'profile'));
    }

    public function printDiakoniaReceipt(MemberAssistance $assistance)
    {
        // Load relationships
        $assistance->load(['member', 'journal.items.account']);

        $profile = \App\Models\ChurchProfile::first() ?? new \App\Models\ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789'
        ]);

        return view('reports.diakonia-receipt', compact('assistance', 'profile'));
    }

    public function printJournalRange(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $journals = Journal::with(['items.account'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('transaction_number', 'asc')
            ->get();

        $profile = \App\Models\ChurchProfile::first() ?? new \App\Models\ChurchProfile([
            'gmit_name' => 'Majelis Sinode GMIT',
            'church_name' => 'Jemaat Sion Oepura',
            'address' => 'Jl. H.R. Koroh, Oepura, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur',
            'phone' => '081123456789',
            'ketua_majelis' => 'Pdt. Sion Oepura, S.Th',
            'sekretaris' => 'Penatua Sekretaris',
            'bendahara' => 'Penatua Bendahara'
        ]);

        return view('reports.journal-range', compact('journals', 'startDate', 'endDate', 'profile'));
    }
}
