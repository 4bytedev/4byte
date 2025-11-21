<?php

namespace Packages\Course\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Packages\React\Traits\HasComments;
use Packages\React\Traits\HasSaves;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $video_url
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int $user_id
 * @property int $chapter_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Packages\Course\Models\CourseChapter $chapter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Save> $saves
 * @property-read int|null $saves_count
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereChapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseLesson whereVideoUrl($value)
 * @mixin \Eloquent
 */
class CourseLesson extends Model implements HasMedia
{
    use HasComments;

    /** @use HasFactory<\Packages\Course\Database\Factories\CourseFactory> */
    use HasFactory;

    use HasSaves;
    use InteractsWithMedia;
    use LogsActivity;
    use Searchable;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'video_url',
        'status',
        'published_at',
        'user_id',
        'chapter_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<CourseChapter, $this>
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(CourseChapter::class, 'chapter_id');
    }

    /**
     * Register media collections and conversions.
     */
    public function registerMediaCollections(): void
    {
        $validateImage = function (File $file, ?array $allowedMimes = null) {
            $allowedMimes ??= ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'];

            if (! in_array($file->mimeType, $allowedMimes)) {
                throw new \Exception("Unsupported file type: {$file->mimeType}");
            }

            if ($file->size > 5 * 1024 * 1024) {
                throw new \Exception('File too large. Max allowed size is 5MB.');
            }

            return true;
        };

        $this->addMediaCollection('content')
            ->withResponsiveImages()
            ->acceptsFile(fn ($file) => $validateImage($file));
    }

    /**
     * Get the activity log options for CourseLesson model.
     * Logs changes to the "title", "slug", "excerpt" and "content" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('course')
            ->logOnly(['title', 'video_url', 'content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Determine should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'PUBLISHED';
    }

    /**
     * Get the search fields for CourseLesson model
     * Searchs between "id" and "title" attributes.
     *
     * @return array{id: int, title: string}
     */
    public function toSearchableArray(): array
    {
        return [
            'id'    => (int) $this->id,
            'title' => $this->title,
        ];
    }
}
