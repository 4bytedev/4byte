<?php

namespace App\Filament\Resources\ArticleDislikeResource\Pages;

use App\Filament\Resources\ArticleDislikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleDislikes extends ListRecords
{
    protected static string $resource = ArticleDislikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
