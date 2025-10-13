<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserFollowResource\Pages;
use App\Models\UserFollow;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserFollowResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = UserFollow::class;

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
            'index' => Pages\ListUserFollows::route('/'),
            'create' => Pages\CreateUserFollow::route('/create'),
            'edit' => Pages\EditUserFollow::route('/{record}/edit'),
        ];
    }
}
