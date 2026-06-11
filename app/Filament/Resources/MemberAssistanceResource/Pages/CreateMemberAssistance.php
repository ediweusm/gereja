<?php

namespace App\Filament\Resources\MemberAssistanceResource\Pages;

use App\Filament\Resources\MemberAssistanceResource;
use App\Models\Journal;
use App\Models\MemberAssistance;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateMemberAssistance extends CreateRecord
{
    protected static string $resource = MemberAssistanceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Journal
            $journal = Journal::create([
                'transaction_date' => $data['transaction_date'],
                'description' => $data['description'],
                'reference_number' => 'DIAKONIA-' . now()->format('YmdHis'),
            ]);

            // 2. Create JournalItem DEBIT (Expense Account)
            $journal->items()->create([
                'account_id' => $data['expense_account_id'],
                'debit' => $data['amount'],
                'credit' => 0,
            ]);

            // 3. Create JournalItem KREDIT (Cash Account)
            $journal->items()->create([
                'account_id' => $data['cash_account_id'],
                'debit' => 0,
                'credit' => $data['amount'],
            ]);

            // 4. Create and return MemberAssistance
            return MemberAssistance::create([
                'journal_id' => $journal->id,
                'member_id' => $data['member_id'],
                'amount' => $data['amount'],
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
