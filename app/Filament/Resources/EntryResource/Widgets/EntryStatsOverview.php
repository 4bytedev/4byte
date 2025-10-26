<?php

namespace App\Filament\Resources\EntryResource\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Packages\Entry\Models\Entry;

class EntryStatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 4;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Entries'), Entry::count())
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),
        ];
    }
}
