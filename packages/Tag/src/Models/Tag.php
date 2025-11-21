<?php

namespace Packages\Tag\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Packages\Article\Models\Article;
use Packages\React\Traits\HasFollowers;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Follow> $followers
 * @property-read int|null $followers_count
 * @property-read \Packages\Tag\Models\TagProfile|null $profile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    /** @use HasFactory<\Packages\Tag\Database\Factories\TagFactory> */
    use HasFactory;

    use HasFollowers;
    use LogsActivity;

    protected $fillable = ['name', 'slug'];

    /**
     * @return BelongsToMany<Article, $this>
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag');
    }

    /**
     * @return HasOne<TagProfile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(TagProfile::class);
    }

    /**
     * Get the activity log options for Tag model.
     * Logs changes to the "name" and "slug" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tag')
            ->logOnly(['name', 'slug'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
