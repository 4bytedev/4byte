<?php

namespace App\Filament\Resources\EntryDislikeResource\Pages;

use App\Filament\Resources\EntryDislikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntryDislike extends EditRecord
{
    protected static string $resource = EntryDislikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
