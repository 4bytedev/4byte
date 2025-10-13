<?php

namespace App\Filament\Resources\EntryCommentLikeResource\Pages;

use App\Filament\Resources\EntryCommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntryCommentLike extends EditRecord
{
    protected static string $resource = EntryCommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
