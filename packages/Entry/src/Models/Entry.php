<?php

namespace Packages\Entry\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Packages\React\Traits\HasCacheKey;
use Packages\React\Traits\HasComments;
use Packages\React\Traits\HasDislikes;
use Packages\React\Traits\HasLikes;
use Packages\React\Traits\HasSaves;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

class Entry extends Model implements HasMedia
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
        'slug',
        'content',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getContentImages()
    {
        $medias = $this->getMedia('content');

        if ($medias->isEmpty()) {
            return [];
        }

        return $medias->map(function ($media) {
            $imageUrl = $media->getFullUrl();
            $srcset = $media->getSrcset();
            $responsive = $media->getResponsiveImageUrls();

            return [
                'image' => $imageUrl,
                'srcset' => $srcset,
                'responsive' => $responsive,
            ];
        })->toArray();
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
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('entry')
            ->logOnly(['content'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
