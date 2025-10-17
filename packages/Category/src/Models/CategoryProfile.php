<?php

namespace Packages\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoryProfile extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['description', 'color', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category_profile')
            ->logOnly(['description', 'color'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
