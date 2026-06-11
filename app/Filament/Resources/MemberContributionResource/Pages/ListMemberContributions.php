<?php

namespace App\Filament\Resources\MemberContributionResource\Pages;

use App\Filament\Resources\MemberContributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberContributions extends ListRecords
{
    protected static string $resource = MemberContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printAdmissionsByRange')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->default(now()->startOfMonth()), // Awal bulan ini
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->default(now()), // Hari ini
                ])
                ->action(function (array $data) {
                    return redirect()->route('reports.admissions_by_range', [
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date']
                    ]);
                }),
        ];
    }
}
