<?php

namespace App\Filament\Resources\TagFollowResource\Pages;

use App\Filament\Resources\TagFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTagFollows extends ListRecords
{
    protected static string $resource = TagFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
