<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleDislikeResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Article\Models\ArticleDislike;

class ArticleDislikeResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = ArticleDislike::class;

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
            'index' => Pages\ListArticleDislikes::route('/'),
            'create' => Pages\CreateArticleDislike::route('/create'),
            'edit' => Pages\EditArticleDislike::route('/{record}/edit'),
        ];
    }
}
