<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Packages\React\Models\Save;

class SaveResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Save::class;

    protected static bool $shouldRegisterNavigation = false;
}
