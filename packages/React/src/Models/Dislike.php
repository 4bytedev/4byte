<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dislike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dislikeable_type',
        'dislikeable_id',
    ];

    public $timestamps = false;

    public function dislikeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
