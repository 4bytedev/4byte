<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Filament\Resources\EntryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::uuid();

        return $data;
    }
}
