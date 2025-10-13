<?php

namespace App\Filament\Resources\EntryCommentResource\Pages;

use App\Filament\Resources\EntryCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntryComment extends EditRecord
{
    protected static string $resource = EntryCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
