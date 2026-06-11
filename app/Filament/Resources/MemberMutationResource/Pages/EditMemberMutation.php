<?php

namespace App\Filament\Resources\MemberMutationResource\Pages;

use App\Filament\Resources\MemberMutationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberMutation extends EditRecord
{
    protected static string $resource = MemberMutationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
