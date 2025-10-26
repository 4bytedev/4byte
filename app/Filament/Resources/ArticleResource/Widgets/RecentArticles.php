<?php

namespace App\Filament\Resources\ArticleResource\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Packages\Article\Models\Article;

class RecentArticles extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 6;

    protected int | array | null $columns = 6;

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.articles.edit', ['record' => $record]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('article.view', ['slug' => $record->slug]))
                ->openUrlInNewTab(),

            Action::make('edit')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record) => route('filament.admin.resources.articles.edit', $record))
                ->color('primary'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
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
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Article::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable(),
            Tables\Columns\BadgeColumn::make('status')->label(__('Status'))
                ->colors([
                    'success' => 'published',
                    'warning' => 'draft',
                ]),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Date'))
                ->dateTime('d M Y, H:i'),
        ];
    }
}
