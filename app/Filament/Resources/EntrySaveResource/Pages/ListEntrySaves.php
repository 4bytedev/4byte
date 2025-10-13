<?php

namespace App\Filament\Resources\EntrySaveResource\Pages;

use App\Filament\Resources\EntrySaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntrySaves extends ListRecords
{
    protected static string $resource = EntrySaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
