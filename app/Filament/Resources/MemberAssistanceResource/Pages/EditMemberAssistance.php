<?php

namespace App\Filament\Resources\MemberAssistanceResource\Pages;

use App\Filament\Resources\MemberAssistanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditMemberAssistance extends EditRecord
{
    protected static string $resource = MemberAssistanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $assistance = $this->getRecord();
        $journal = $assistance->journal;
        if ($journal) {
            $data['transaction_date'] = $journal->transaction_date?->format('Y-m-d');
            $data['description'] = $journal->description;

            $debitItem = $journal->items()->where('debit', '>', 0)->first();
            if ($debitItem) {
                $data['expense_account_id'] = $debitItem->account_id;
            }

            $creditItem = $journal->items()->where('credit', '>', 0)->first();
            if ($creditItem) {
                $data['cash_account_id'] = $creditItem->account_id;
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::transaction(function () use ($record, $data) {
            // Update assistance amount and member
            $record->update([
                'member_id' => $data['member_id'],
                'amount' => $data['amount'],
            ]);

            // Update associated journal
            $journal = $record->journal;
            if ($journal) {
                $journal->update([
                    'transaction_date' => $data['transaction_date'],
                    'description' => $data['description'],
                ]);

                // Update DEBIT item (Expense)
                $debitItem = $journal->items()->where('debit', '>', 0)->first();
                if ($debitItem) {
                    $debitItem->update([
                        'account_id' => $data['expense_account_id'],
                        'debit' => $data['amount'],
                    ]);
                }

                // Update KREDIT item (Cash)
                $creditItem = $journal->items()->where('credit', '>', 0)->first();
                if ($creditItem) {
                    $creditItem->update([
                        'account_id' => $data['cash_account_id'],
                        'credit' => $data['amount'],
                    ]);
                }
            }
        });

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
