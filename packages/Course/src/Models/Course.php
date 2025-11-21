<?php

namespace Packages\Course\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Packages\Category\Models\Category;
use Packages\React\Traits\HasComments;
use Packages\React\Traits\HasDislikes;
use Packages\React\Traits\HasLikes;
use Packages\React\Traits\HasSaves;
use Packages\Tag\Models\Tag;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

class Course extends Model implements HasMedia
{
    use HasComments;
    use HasDislikes;

    /** @use HasFactory<\Packages\Course\Database\Factories\CourseFactory> */
    use HasFactory;

    use HasLikes;
    use HasSaves;
    use InteractsWithMedia;
    use LogsActivity;
    use Searchable;

    protected $fillable = [
        'title',
        'slug',
        'difficulty',
        'excerpt',
        'content',
        'status',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CourseChapter, $this>
     */
    public function chapters()
    {
        return $this->hasMany(CourseChapter::class, 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CourseLesson, $this>
     */
    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany<Category, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_category');
    }

    /**
     * @return BelongsToMany<Tag, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'course_tag');
    }

    /**
     * Get the cover image of the course.
     *
     * @return array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null}
     */
    public function getCoverImage()
    {
        $media = $this->getFirstMedia('cover');

        if (! $media) {
            return ['image' => '', 'thumb' => null, 'srcset' => '', 'responsive' => ''];
        }

        $thumbUrl = null;
        if ($media->hasGeneratedConversion('thumb')) {
            $thumbUrl = $media->getFullUrl('thumb');
        }

        $imageUrl   = $media->getFullUrl();
        $srcset     = $media->getSrcset();
        $responsive = $media->getResponsiveImageUrls();

        return [
            'image'      => $imageUrl,
            'thumb'      => $thumbUrl,
            'srcset'     => $srcset,
            'responsive' => $responsive,
        ];
    }

    /**
     * Register media collections and conversions.
     */
    public function registerMediaCollections(): void
    {
        $validateImage = function (File $file, ?array $allowedMimes = null) {
            $allowedMimes ??= ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

            if (! in_array($file->mimeType, $allowedMimes)) {
                throw new \Exception("Unsupported file type: {$file->mimeType}");
            }

            if ($file->size > 5 * 1024 * 1024) {
                throw new \Exception('File too large. Max allowed size is 5MB.');
            }

            return true;
        };

        $this->addMediaCollection('cover')
            ->withResponsiveImages()
            ->singleFile()
            ->acceptsFile(fn ($file) => $validateImage($file));
        /* @phpstan-ignore-next-line */
        $this->addMediaConversion('thumb')
            ->width(256)
            ->height(256)
            ->sharpen(10)
            ->performOnCollections('cover')
            ->queued();
    }

    /**
     * Get the activity log options for Course model.
     * Logs changes to the "title", "slug", "difficulty" and "content" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('course')
            ->logOnly(['title', 'slug', 'difficulty', 'content'])
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
     * Get the search fields for Course model
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
