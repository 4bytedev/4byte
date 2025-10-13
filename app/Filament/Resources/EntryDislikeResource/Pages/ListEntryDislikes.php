<?php

namespace App\Filament\Resources\EntryDislikeResource\Pages;

use App\Filament\Resources\EntryDislikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntryDislikes extends ListRecords
{
    protected static string $resource = EntryDislikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
