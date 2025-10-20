<?php

namespace Packages\Tag\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Packages\Category\Models\Category;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $description
 * @property string $color
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Tag $tag
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagProfile whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TagProfile extends Model
{
    /** @use HasFactory<\Packages\Tag\Database\Factories\TagProfileFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = ['description', 'color', 'tag_id'];

    /**
     * @return BelongsToMany<Category, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'tag_profile_category');
    }

    /**
     * @return BelongsTo<Tag, $this>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Get the activity log options for TagProfile model.
     * Logs changes to the "description" and "color" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tag_profile')
            ->logOnly(['description', 'color'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
