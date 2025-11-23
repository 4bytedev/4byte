<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseChapterResource\Pages;
use App\Filament\Resources\CourseChapterResource\RelationManagers\LessonsRelationManager;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Course\Models\CourseChapter;

class CourseChapterResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = CourseChapter::class;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationParentItem(): string
    {
        return __('Courses');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main Content'))
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('course.title')->label('Course'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LessonsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourseChapters::route('/'),
            'create' => Pages\CreateCourseChapter::route('/create'),
            'edit'   => Pages\EditCourseChapter::route('/{record}/edit'),
        ];
    }
}
