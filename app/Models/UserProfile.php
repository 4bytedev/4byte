<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

/**
 * @property int $id
 * @property string $role
 * @property string $bio
 * @property string $location
 * @property string $website
 * @property array<array-key, mixed> $socials
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\UserProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereSocials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereWebsite($value)
 * @mixin \Eloquent
 */
class UserProfile extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'role',
        'bio',
        'location',
        'website',
        'socials',
        'cover',
        'user_id',
    ];

    protected $casts = [
        'socials' => 'array',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cover image of the user profile.
     *
     * @return array{image: string, responsive: string|array<int, string>, srcset: string}
     */
    public function getCoverImage()
    {
        $media = $this->getFirstMedia('cover');

        if (! $media) {
            return ['image' => '', 'srcset' => '', 'responsive' => ''];
        }

        $imageUrl   = $media->getFullUrl();
        $srcset     = $media->getSrcset();
        $responsive = $media->getResponsiveImageUrls();

        return [
            'image'      => $imageUrl,
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
    }

    /**
     * Get the activity log options for UserProfile model.
     * Logs changes to the "role", "bio", "location", "website" and "socials" attributes.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user_profile')
            ->logOnly(['role', 'bio', 'location', 'website', 'socials'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
