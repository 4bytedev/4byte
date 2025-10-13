<?php

namespace App\Filament\Resources\UserFollowResource\Pages;

use App\Filament\Resources\UserFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserFollows extends ListRecords
{
    protected static string $resource = UserFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
