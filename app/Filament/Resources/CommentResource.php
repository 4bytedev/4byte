<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Packages\React\Models\Comment;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class CommentResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('Comments');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main Content'))
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                MarkdownEditor::make('content')
                                    ->label(__('Content'))
                                    ->extraAttributes(['style' => 'min-height: 790px;']),

                                Forms\Components\Select::make('user_id')
                                    ->searchable()
                                    ->required()
                                    ->label(__('User'))
                                    ->relationship('user', 'name'),
                                Forms\Components\Select::make('parent_id')
                                    ->searchable()
                                    ->required()
                                    ->label(__('Parent'))
                                    ->relationship('parent', 'name'),

                                Forms\Components\TextInput::make('commentable_type')
                                    ->required()
                                    ->label(__('Commentable Type')),
                                Forms\Components\TextInput::make('commentable_id')
                                    ->required()
                                    ->label(__('Commentable ID')),

                            ]),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('commentable_type')
                    ->label(__('Commentable Type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('commentable_id')
                    ->sortable()
                    ->label(__('Commentable ID')),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(['DRAFT' => 'Draft', 'PUBLISHED' => 'Published', 'PENDING' => 'Pending']),
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
            'index'  => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit'   => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
