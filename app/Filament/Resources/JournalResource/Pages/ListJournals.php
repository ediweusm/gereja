<?php

namespace App\Filament\Resources\JournalResource\Pages;

use App\Filament\Resources\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('printRange')
                ->label('Cetak Jurnal')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->required()
                        ->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->required()
                        ->default(now()->endOfMonth()),
                ])                
                ->action(function (array $data, $livewire) {
                    // 1. Buat URL target cetaknya
                    $url = route('reports.journal_range', [
                        'start_date' => $data['start_date'],
                        'end_date'   => $data['end_date'],
                    ]);

                    // 2. Perintahkan browser untuk membuka URL tersebut di TAB BARU
                    $livewire->js("window.open('{$url}', '_blank');");
                }),
                
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
