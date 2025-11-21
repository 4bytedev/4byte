<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $user_id
 * @property string $saveable_type
 * @property int $saveable_id
 *
 * @property-read Model $saveable
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save whereSaveableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save whereSaveableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Save whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Save extends Model
{
    /** @use HasFactory<\Packages\React\Database\Factories\SaveFactory> */
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'saveable_type',
        'saveable_id',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function saveable(): MorphTo
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
