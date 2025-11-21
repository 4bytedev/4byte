<?php

namespace Packages\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CourseLesson> $lessons
 * @property-read int|null $lessons_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseChapter whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
