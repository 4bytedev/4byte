<?php

namespace Packages\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CourseChapter extends Model
{
    /** @use HasFactory<\Packages\Course\Database\Factories\CourseFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = [
        'title',
        'course_id',
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * @return HasMany<CourseLesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class, 'chapter_id');
    }

    /**
     * Get the activity log options for CourseChapter model.
     * Logs changes to the "title" attribute.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('course-chapter')
            ->logOnly(['title'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
