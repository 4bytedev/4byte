<?php

namespace App\Filament\Resources\EntryCommentLikeResource\Pages;

use App\Filament\Resources\EntryCommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntryCommentLikes extends ListRecords
{
    protected static string $resource = EntryCommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
