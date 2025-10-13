<?php

namespace App\Filament\Resources\UserFollowResource\Pages;

use App\Filament\Resources\UserFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserFollow extends EditRecord
{
    protected static string $resource = UserFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
