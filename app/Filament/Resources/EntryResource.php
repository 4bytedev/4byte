<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntryResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Entry\Models\Entry;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class EntryResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Entry::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-asia-australia';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('Entries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main Content'))
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('content')
                            ->collection('content')
                            ->required()
                            ->label(__('Content'))
                            ->extraAttributes(['style' => 'min-height: 300px;']),

                        Forms\Components\Select::make('user_id')
                            ->searchable()
                            ->required()
                            ->label(__('User'))
                            ->relationship('user', 'name'),
                    ])
                    ->columnSpan(9),
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
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->grow(false),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->multiple(),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEntries::route('/'),
            'create' => Pages\CreateEntry::route('/create'),
            'edit'   => Pages\EditEntry::route('/{record}/edit'),
        ];
    }
}
