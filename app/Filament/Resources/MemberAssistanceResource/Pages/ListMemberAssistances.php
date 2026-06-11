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
        ];
    }
}
