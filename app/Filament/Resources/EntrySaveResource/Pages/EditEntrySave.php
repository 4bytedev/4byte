<?php

namespace App\Filament\Resources\EntrySaveResource\Pages;

use App\Filament\Resources\EntrySaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntrySave extends EditRecord
{
    protected static string $resource = EntrySaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
