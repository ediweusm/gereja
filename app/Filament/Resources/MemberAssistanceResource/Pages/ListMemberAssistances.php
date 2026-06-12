<?php

namespace App\Filament\Resources\MemberAssistanceResource\Pages;

use App\Filament\Resources\MemberAssistanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberAssistances extends ListRecords
{
    protected static string $resource = MemberAssistanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printAssistancesByRange')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data, $livewire) {
                    // Buat URL target cetak (Pastikan route ini didaftarkan di routes/web.php)
                    $url = route('reports.assistances_by_range', [
                        'start_date' => $data['start_date'],
                        'end_date'   => $data['end_date'],
                    ]);

                    // Perintahkan browser membuka URL di TAB BARU
                    $livewire->js("window.open('{$url}', '_blank');");
                }),        
        ];
    }
}