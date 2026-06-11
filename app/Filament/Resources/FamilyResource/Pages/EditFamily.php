<?php

namespace App\Filament\Resources\FamilyResource\Pages;

use App\Filament\Resources\FamilyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamily extends EditRecord
{
    protected static string $resource = FamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label('Cetak KK')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (\App\Models\Family $record) => route('family.print', $record))
                ->openUrlInNewTab(),
        ];
    }
}
