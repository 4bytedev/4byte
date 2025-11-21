<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\ChaptersRelationManager;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Forms\Components\SpatieMediaLibraryMarkdownEditor;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Course\Models\Course;

class CourseResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
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
                                            ->unique(Course::class, 'slug', fn ($record) => $record)
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('generateSlug')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->tooltip(__('Generate from slug'))
                                                    ->action(function ($state, $set, $get) {
                                                        $set('slug', \Str::slug($get('title')));
                                                    })
                                            ),
                                    ]),

                                Forms\Components\Textarea::make('excerpt')
                                    ->label(__('Excerpt')),

                                SpatieMediaLibraryMarkdownEditor::make('content')
                                    ->label(__('Content'))
                                    ->extraAttributes(['style' => 'min-height: 790px;'])
                                    ->collection('content'),
                            ])
                            ->columnSpan(9),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('Course Settings'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('image')
                                            ->label(__('Image'))
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('150')
                                            ->dehydrated(false)
                                            ->collection('cover'),

                                        Forms\Components\Select::make('categories')
                                            ->label(__('Categories'))
                                            ->multiple()
                                            ->relationship('categories', 'name'),

                                        Forms\Components\Select::make('tags')
                                            ->label(__('Tags'))
                                            ->multiple()
                                            ->relationship('tags', 'name'),

                                        Forms\Components\Select::make('user_id')
                                            ->searchable()
                                            ->required()
                                            ->label(__('User'))
                                            ->relationship('user', 'name'),

                                        Forms\Components\Select::make('difficulty')
                                            ->required()
                                            ->label(__('Difficulty'))
                                            ->options([
                                                'EASY'     => 'Easy',
                                                'MEDIUM'   => 'Medium',
                                                'ADVANCED' => 'Advanced',
                                            ]),

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
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Image'))
                    ->grow(false)
                    ->getStateUsing(function ($record) {
                        return $record->getFirstMediaUrl('cover');
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('difficulty')
                    ->label(__('Difficulty'))
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label(__('Categories'))
                    ->multiple()
                    ->relationship('categories', 'name'),
                Tables\Filters\SelectFilter::make('tags')
                    ->label(__('Tags'))
                    ->multiple()
                    ->relationship('tags', 'name'),
                Tables\Filters\SelectFilter::make('user')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label(__('Difficulty'))
                    ->options([
                        'EASY'     => 'Easy',
                        'MEDIUM'   => 'Medium',
                        'ADVANCED' => 'Advanced',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ChaptersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
