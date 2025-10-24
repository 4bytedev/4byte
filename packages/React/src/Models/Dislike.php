<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $user_id
 * @property string $dislikeable_type
 * @property int $dislikeable_id
 * @property-read Model $dislikeable
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike whereDislikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike whereDislikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dislike whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Dislike extends Model
{
    /** @use HasFactory<\Packages\React\Database\Factories\DislikeFactory> */
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'dislikeable_type',
        'dislikeable_id',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function dislikeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
