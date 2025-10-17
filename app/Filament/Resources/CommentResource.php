<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Packages\React\Models\Comment;

class CommentResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Comment::class;

    protected static bool $shouldRegisterNavigation = false;
}
