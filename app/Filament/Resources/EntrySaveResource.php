<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntrySaveResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\Entry\Models\EntrySave;

class EntrySaveResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = EntrySave::class;

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
            'index' => Pages\ListEntrySaves::route('/'),
            'create' => Pages\CreateEntrySave::route('/create'),
            'edit' => Pages\EditEntrySave::route('/{record}/edit'),
        ];
    }
}
