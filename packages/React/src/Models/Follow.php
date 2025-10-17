<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follow extends Model
{
    use HasFactory;

    /** @phpstan-ignore-next-line */
    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'followable_id',
        'followable_type',
    ];

    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
