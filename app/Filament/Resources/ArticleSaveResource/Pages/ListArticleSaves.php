<?php

namespace App\Filament\Resources\ArticleSaveResource\Pages;

use App\Filament\Resources\ArticleSaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleSaves extends ListRecords
{
    protected static string $resource = ArticleSaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
