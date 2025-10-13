<?php

namespace App\Filament\Resources\ArticleDislikeResource\Pages;

use App\Filament\Resources\ArticleDislikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleDislike extends EditRecord
{
    protected static string $resource = ArticleDislikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
