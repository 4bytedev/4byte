<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Packages\React\Models\Like;

class LikeResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Like::class;

    protected static bool $shouldRegisterNavigation = false;
}
