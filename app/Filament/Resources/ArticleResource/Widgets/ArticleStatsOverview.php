<?php

namespace App\Filament\Resources\ArticleResource\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Packages\Article\Models\Article;

class ArticleStatsOverview extends BaseWidget
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
            Stat::make(__('Articles'), Article::where('status', 'PUBLISHED')->count())
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),
        ];
    }
}
