<?php

namespace App\Filament\Resources\ArticleCommentLikeResource\Pages;

use App\Filament\Resources\ArticleCommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleCommentLikes extends ListRecords
{
    protected static string $resource = ArticleCommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
