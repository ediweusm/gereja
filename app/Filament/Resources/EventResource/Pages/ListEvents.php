<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printByRange')
                ->label('Cetak Jadwal')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->default(now()->startOfWeek()), // Senin minggu ini
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->default(now()->startOfWeek()->addDays(6)), // Minggu minggu ini
                ])
                ->action(function (array $data) {
                    // Redirect ke route cetak dengan parameter rentang tanggal
                    return redirect()->route('events.print_by_range', [
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date']
                    ]);
                }),
        ];
    }
}
