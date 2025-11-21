<?php

namespace Packages\Course\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Packages\Course\Models\Course;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Services\GorseService;

class CourseObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "saved" event for the Course model.
     */
    public function saved(Course $course): void
    {
        if ($course->status != "PUBLISHED") return;
        $gorseItem = new GorseItem(
            'course:' . $course->id,
            ['course', "user:{$course->user_id}"],
            $course->tags->pluck('id')
                ->map(fn ($id) => 'tag:' . $id)
                ->merge(
                    $course->categories->pluck('id')
                        ->map(fn ($id) => 'category:' . $id)
                )
                ->merge(['article', "user:{$course->user_id}"])
                ->all(),
            $course->slug,
            false,
            Carbon::parse($course->published_at)->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    /**
     * Handle the "updating" event for the Course model.
     */
    public function updating(Course $course): void
    {
        if ($course->isDirty('image')) {
            $oldMedia = $course->getFirstMedia('article');
            if ($oldMedia) {
                $oldMedia->delete();
            }
        }
    }

    /**
     * Handle the "updated" event for the Course model.
     */
    public function updated(Course $course): void
    {
        Cache::forget("course:{$course->id}");
    }

    /**
     * Handle the "deleted" event for the Article model.
     */
    public function deleted(Course $course): void
    {
        $this->gorse->deleteItem("course:{$course->id}");
        Cache::forget("course:{$course->slug}:id");
        Cache::forget("course:{$course->id}");
        Cache::forget("course:{$course->id}:likes");
        Cache::forget("course:{$course->id}:dislikes");
    }
}
