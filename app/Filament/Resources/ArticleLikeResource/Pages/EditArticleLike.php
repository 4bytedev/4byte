<?php

namespace App\Filament\Resources\ArticleLikeResource\Pages;

use App\Filament\Resources\ArticleLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleLike extends EditRecord
{
    protected static string $resource = ArticleLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
