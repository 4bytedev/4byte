<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Packages\React\Models\Dislike;

class DislikeResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Dislike::class;

    protected static bool $shouldRegisterNavigation = false;
}
