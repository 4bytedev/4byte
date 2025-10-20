<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Packages\React\Models\Follow;

class FollowResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Follow::class;

    protected static bool $shouldRegisterNavigation = false;
}
