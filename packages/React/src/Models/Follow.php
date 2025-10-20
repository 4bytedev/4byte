<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $follower_id
 * @property string $followable_type
 * @property int $followable_id
 * @property-read \Illuminate\Database\Eloquent\Model $followable
 * @property-read User $follower
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowerId($value)
 * @mixin \Eloquent
 */
class Follow extends Model
{
    /** @use HasFactory<\Packages\React\Database\Factories\FollowFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'followable_id',
        'followable_type',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function followable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
