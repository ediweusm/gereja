<?php

namespace App\Filament\Resources\MinistryRoleResource\Pages;

use App\Filament\Resources\MinistryRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMinistryRoles extends ListRecords
{
    protected static string $resource = MinistryRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
