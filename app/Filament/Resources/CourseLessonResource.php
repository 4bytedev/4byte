<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseLessonResource\Pages;
use App\Forms\Components\SpatieMediaLibraryMarkdownEditor;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Course\Models\CourseLesson;

class CourseLessonResource extends Resource
{
    protected static ?string $model = CourseLesson::class;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('Courses');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\Section::make(__('Main Content'))
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label(__('Title'))
                                            ->required()
                                            ->reactive(),
                                        Forms\Components\TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->unique(CourseLesson::class, 'slug', fn ($record) => $record)
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('generateSlug')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->tooltip(__('Generate from slug'))
                                                    ->action(function ($state, $set, $get) {
                                                        $set('slug', \Str::slug($get('title')));
                                                    })
                                            ),
                                    ]),

                                Forms\Components\TextInput::make('video_url')
                                    ->label(__('Video Url'))
                                    ->url(),

                                SpatieMediaLibraryMarkdownEditor::make('content')
                                    ->label(__('Content'))
                                    ->extraAttributes(['style' => 'min-height: 790px;'])
                                    ->collection('content'),
                            ])
                            ->columnSpan(9),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('Lesson Settings'))
                                    ->schema([
                                        Forms\Components\Select::make('chapter_id')
                                            ->required()
                                            ->searchable()
                                            ->label('Chapter')
                                            ->relationship('chapter', 'title'),

                                        Forms\Components\Select::make('user_id')
                                            ->searchable()
                                            ->required()
                                            ->label(__('User'))
                                            ->relationship('user', 'name'),

                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->label(__('Status'))
                                            ->options(['DRAFT' => 'Draft', 'PUBLISHED' => 'Publıshed', 'PENDING' => 'Pending'])
                                            ->default('DRAFT')
                                            ->live()
                                            ->afterStateUpdated(function (string $state, callable $set) {
                                                if ($state === 'PUBLISHED') {
                                                    $set('published_at', Carbon::now());
                                                } else {
                                                    $set('published_at', null);
                                                }
                                            }),

                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label(__('Published At'))
                                            ->required(fn ($get) => $get('status') === 'PENDING'),
                                    ]),
                            ])->columnSpan(3),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('Author'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourseLessons::route('/'),
            'create' => Pages\CreateCourseLesson::route('/create'),
            'edit'   => Pages\EditCourseLesson::route('/{record}/edit'),
        ];
    }
}
