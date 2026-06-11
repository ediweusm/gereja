<?php

namespace App\Filament\Resources\MemberMutationResource\Pages;

use App\Filament\Resources\MemberMutationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;

class ListMemberMutations extends ListRecords
{
    protected static string $resource = MemberMutationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printMutationsByRange')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('reports.mutations_by_range', [
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                    ]);
                }),
        ];
    }
}
