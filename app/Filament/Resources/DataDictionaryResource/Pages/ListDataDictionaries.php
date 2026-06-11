<?php

namespace App\Filament\Resources\DataDictionaryResource\Pages;

use App\Filament\Resources\DataDictionaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataDictionaries extends ListRecords
{
    protected static string $resource = DataDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
