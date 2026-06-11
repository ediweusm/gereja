<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('printMembers')
                ->label('Cetak Jemaat')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    Select::make('membership_status_id')
                        ->relationship('membershipStatus', 'label', fn ($query) => $query->category('membership_status')->active())
                        ->label('Filter Status (Kosongkan untuk semua)'),
                    Select::make('gender')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->label('Filter L/P (Kosongkan untuk semua)'),
                ])
                ->action(function (array $data) {
                    $url = route('reports.members_list', [
                        'membership_status_id' => $data['membership_status_id'] ?? null,
                        'gender' => $data['gender'] ?? null,
                    ]);
                    $this->js("window.open('{$url}', '_blank');");
                }),
        ];
    }
}
