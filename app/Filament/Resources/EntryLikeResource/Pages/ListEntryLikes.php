<?php

namespace App\Filament\Resources\EntryLikeResource\Pages;

use App\Filament\Resources\EntryLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntryLikes extends ListRecords
{
    protected static string $resource = EntryLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
