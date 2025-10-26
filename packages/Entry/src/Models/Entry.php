<?php

namespace Packages\Entry\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Packages\React\Traits\HasComments;
use Packages\React\Traits\HasDislikes;
use Packages\React\Traits\HasLikes;
use Packages\React\Traits\HasSaves;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

/**
 * @property int $id
 * @property string $slug
 * @property string|null $content
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Dislike> $dislikes
 * @property-read int|null $dislikes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Like> $likes
 * @property-read int|null $likes_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Packages\React\Models\Save> $saves
 * @property-read int|null $saves_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Entry extends Model implements HasMedia
{
    use HasComments;
    use HasDislikes;

    /** @use HasFactory<\Packages\Entry\Database\Factories\EntryFactory> */
    use HasFactory;

    use HasLikes;
    use HasSaves;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'slug',
        'content',
        'user_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get images of entry.
     *
     * @return array<int, array{image: string, responsive: string|array<int, string>, srcset: string}>
     */
    public function getContentImages(): array
    {
        $medias = $this->getMedia('content');

        if ($medias->isEmpty()) {
            return [];
        }

        return $medias->map(function ($media) {
            $imageUrl   = $media->getFullUrl();
            $srcset     = $media->getSrcset();
            $responsive = $media->getResponsiveImageUrls();

            return [
                'image'      => $imageUrl,
                'srcset'     => $srcset,
                'responsive' => $responsive,
            ];
        })->toArray();
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
     * Get the activity log options for Entry model.
     * Logs changes to the "content" attribute.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('entry')
            ->logOnly(['content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
