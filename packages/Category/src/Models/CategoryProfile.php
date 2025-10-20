<?php

namespace Packages\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $description
 * @property string $color
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Packages\Category\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoryProfile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryProfile extends Model
{
    /** @use HasFactory<\Packages\Category\Database\Factories\CategoryProfileFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = ['description', 'color', 'category_id'];

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the activity log options for CategoryProfile model.
     * Logs changes to the "description" and "color" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category_profile')
            ->logOnly(['description', 'color'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
