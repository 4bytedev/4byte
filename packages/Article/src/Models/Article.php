<?php

namespace Packages\Article\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Packages\Category\Models\Category;
use Packages\React\Traits\HasCacheKey;
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

class Article extends Model implements HasMedia
{
    use HasCacheKey;
    use HasComments;
    use HasDislikes;
    use HasFactory;
    use HasLikes;
    use HasSaves;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'sources',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'sources' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function getCoverImage()
    {
        $media = $this->getFirstMedia('cover');

        if (! $media) {
            return ['image' => '', 'thumb' => '', 'srcset' => '', 'responsive' => ''];
        }

        $thumbUrl = null;
        if ($media->hasGeneratedConversion('thumb')) {
            $thumbUrl = $media->getFullUrl('thumb');
        }

        $imageUrl = $media->getFullUrl();
        $srcset = $media->getSrcset();
        $responsive = $media->getResponsiveImageUrls();

        return [
            'image' => $imageUrl,
            'thumb' => $thumbUrl,
            'srcset' => $srcset,
            'responsive' => $responsive,
        ];
    }

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

        /** @phpstan-ignore-next-line */
        $this->addMediaConversion('thumb')
            ->width(256)
            ->height(256)
            ->sharpen(10)
            ->queued();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('article')
            ->logOnly(['title', 'slug', 'excerpt', 'content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
