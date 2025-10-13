<?php

namespace App\Filament\Resources\ArticleCommentLikeResource\Pages;

use App\Filament\Resources\ArticleCommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleCommentLike extends EditRecord
{
    protected static string $resource = ArticleCommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
