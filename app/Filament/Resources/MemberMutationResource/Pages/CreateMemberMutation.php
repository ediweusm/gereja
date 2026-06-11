<?php

namespace App\Filament\Resources\MemberMutationResource\Pages;

use App\Filament\Resources\MemberMutationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMemberMutation extends CreateRecord
{
    protected static string $resource = MemberMutationResource::class;
}
