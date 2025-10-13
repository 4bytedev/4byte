<?php

namespace App\Filament\Resources\EntryLikeResource\Pages;

use App\Filament\Resources\EntryLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntryLike extends EditRecord
{
    protected static string $resource = EntryLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
