<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleLikeResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Article\Models\ArticleLike;

class ArticleLikeResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = ArticleLike::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleLikes::route('/'),
            'create' => Pages\CreateArticleLike::route('/create'),
            'edit' => Pages\EditArticleLike::route('/{record}/edit'),
        ];
    }
}
