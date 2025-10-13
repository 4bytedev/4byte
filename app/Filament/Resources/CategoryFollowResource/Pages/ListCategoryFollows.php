<?php

namespace App\Filament\Resources\CategoryFollowResource\Pages;

use App\Filament\Resources\CategoryFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryFollows extends ListRecords
{
    protected static string $resource = CategoryFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
