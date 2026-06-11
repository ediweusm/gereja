<?php

namespace App\Filament\Resources\MemberContributionResource\Pages;

use App\Filament\Resources\MemberContributionResource;
use App\Models\Journal;
use App\Models\MemberContribution;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateMemberContribution extends CreateRecord
{
    protected static string $resource = MemberContributionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Journal
            $journal = Journal::create([
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'],
                'reference_number' => 'KONTRIBUSI-' . now()->format('YmdHis'),
            ]);

            // 2. Create JournalItem for DEBIT (Cash Account)
            $journal->items()->create([
                'account_id' => $data['cash_account_id'],
                'debit' => $data['amount'],
                'credit' => 0,
            ]);

            // 3. Create JournalItem for KREDIT (Revenue Account)
            $journal->items()->create([
                'account_id' => $data['revenue_account_id'],
                'debit' => 0,
                'credit' => $data['amount'],
            ]);

            // 4. Create and return MemberContribution
            return MemberContribution::create([
                'journal_id' => $journal->id,
                'member_id' => $data['member_id'],
                'contribution_type_id' => $data['contribution_type_id'],
                'amount' => $data['amount'],
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
