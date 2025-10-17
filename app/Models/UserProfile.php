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

class UserProfile extends Model implements HasMedia
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImage()
    {
        $media = $this->getFirstMedia('cover');

        if (! $media) {
            return [];
        }

        $imageUrl = $media->getFullUrl();
        $srcset = $media->getSrcset();
        $responsive = $media->getResponsiveImageUrls();

        return [
            'image' => $imageUrl,
            'srcset' => $srcset,
            'responsive' => $responsive,
        ];
    }

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user_profile')
            ->logOnly(['role', 'bio', 'location', 'website', 'socials'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
