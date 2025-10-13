<?php

namespace Packages\Tag\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Packages\Category\Models\Category;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TagProfile extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['description', 'color', 'category_id', 'tag_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tag_profile')
            ->logOnly(['description', 'color'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
