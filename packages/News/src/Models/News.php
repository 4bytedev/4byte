<?php

namespace Packages\News\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Packages\Category\Models\Category;
use Packages\Tag\Models\Tag;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $content
 * @property string $image
 * @property string $status
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUserId($value)
 * @mixin \Eloquent
 */
class News extends Model implements HasMedia
{
    /** @use HasFactory<\Packages\News\Database\Factories\NewsFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'status', 'published_at', 'user_id',
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
     * @return BelongsToMany<Category, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'news_category');
    }

    /**
     * @return BelongsToMany<Tag, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    /**
     * Get the cover image of the news.
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

        $this->addMediaCollection('cover')
            ->withResponsiveImages()
            ->singleFile()
            ->acceptsFile(fn ($file) => $validateImage($file, ['image/jpeg', 'image/png', 'image/webp', 'image/avif']));

        /* @phpstan-ignore-next-line */
        $this->addMediaConversion('thumb')
            ->width(256)
            ->height(256)
            ->sharpen(10)
            ->queued();
    }

    /**
     * Get the activity log options for News model.
     * Logs changes to the "title", "slug", "excerpt" and "content" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('news')
            ->logOnly(['title', 'slug', 'excerpt', 'content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
