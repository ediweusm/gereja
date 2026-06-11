<?php

namespace App\Filament\Resources\DataDictionaryResource\Pages;

use App\Filament\Resources\DataDictionaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataDictionary extends EditRecord
{
    protected static string $resource = DataDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
