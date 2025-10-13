<?php

namespace App\Filament\Resources\TagFollowResource\Pages;

use App\Filament\Resources\TagFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagFollow extends EditRecord
{
    protected static string $resource = TagFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
