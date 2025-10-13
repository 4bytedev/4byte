<?php

namespace Packages\News\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Packages\Category\Models\Category;
use Packages\Tag\Models\Tag;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'status', 'published_at', 'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return;
        }

        return Storage::url($this->image);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'news_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('news')
            ->logOnly(['title', 'slug', 'excerpt', 'content', 'image'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
