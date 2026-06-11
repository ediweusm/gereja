<?php

namespace App\Filament\Resources\JournalResource\Pages;

use App\Filament\Resources\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournal extends EditRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label('Cetak Bukti')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (\App\Models\Journal $record) => route('journal.print', $record))
                ->openUrlInNewTab(),
            Actions\Action::make('kwitansi')
                ->label('Cetak Kwitansi')
                ->icon('heroicon-o-ticket')
                ->color('warning')
                ->url(fn (\App\Models\Journal $record) => route('journal.kwitansi', $record))
                ->openUrlInNewTab(),
        ];
    }
}
