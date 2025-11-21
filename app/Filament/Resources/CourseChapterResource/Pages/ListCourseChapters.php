<?php

namespace App\Filament\Resources\CourseChapterResource\Pages;

use App\Filament\Resources\CourseChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseChapters extends ListRecords
{
    protected static string $resource = CourseChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
