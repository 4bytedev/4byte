<?php

namespace App\Filament\Resources\ArticleLikeResource\Pages;

use App\Filament\Resources\ArticleLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleLikes extends ListRecords
{
    protected static string $resource = ArticleLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
