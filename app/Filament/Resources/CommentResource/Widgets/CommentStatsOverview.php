<?php

namespace App\Filament\Resources\CommentResource\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Packages\React\Models\Comment;

class CommentStatsOverview extends BaseWidget
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
            Stat::make(__('Comments'), Comment::count())
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),
        ];
    }
}
