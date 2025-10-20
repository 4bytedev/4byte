<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Tag\Models\Tag;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class TagResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tags');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Tag'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('Name'))
                            ->reactive(),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->label(__('Slug'))
                            ->unique(Tag::class, 'slug', fn ($record) => $record)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateSlug')
                                    ->icon('heroicon-o-arrow-path')
                                    ->tooltip(__('Generate from name'))
                                    ->action(function ($state, $set, $get) {
                                        $set('slug', \Str::slug($get('name')));
                                    })
                            ),
                    ]),
                Forms\Components\Section::make(__('Tag Profile'))
                    ->relationship('profile')
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->label(__('Color'))
                            ->required(),
                        Forms\Components\Select::make('categories')
                            ->relationship('categories', 'name')
                            ->label(__('Category'))
                            ->searchable()
                            ->multiple()
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),

                Tables\Columns\ColorColumn::make('profile.color')
                    ->label(__('Color')),

                Tables\Columns\TextColumn::make('profile.category.name')
                    ->label(__('Category')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
