<?php

namespace App\Filament\Resources\PastorResource\Pages;

use App\Filament\Resources\PastorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPastor extends EditRecord
{
    protected static string $resource = PastorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
