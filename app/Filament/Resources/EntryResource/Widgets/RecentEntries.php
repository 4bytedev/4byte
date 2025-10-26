<?php

namespace App\Filament\Resources\EntryResource\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Packages\Entry\Models\Entry;

class RecentEntries extends BaseWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 6;

    protected int | array | null $columns = 6;

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.entries.edit', ['record' => $record]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('entry.view', ['slug' => $record->slug]))
                ->openUrlInNewTab(),

            Action::make('edit')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record) => route('filament.admin.resources.entries.edit', $record))
                ->color('primary'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('user')
                ->label(__('User'))
                ->relationship('user', 'name')
                ->multiple(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Entry::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('slug')->label(__('Slug'))->searchable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label(__('User')),
        ];
    }
}
