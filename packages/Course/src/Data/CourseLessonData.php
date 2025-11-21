<?php

namespace Packages\Course\Data;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Packages\Course\Models\CourseLesson;
use Spatie\LaravelData\Data;

class CourseLessonData extends Data
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public ?string $content,
        public ?string $video_url,
        public ?DateTime $published_at,
        public bool $isSaved,
        public bool $canUpdate,
        public bool $canDelete,
        public string $type = 'course'
    ) {
    }

    /**
     * Create a TagData instance from a Tag model.
     */
    public static function fromModel(CourseLesson $lesson, bool $setId = false): self
    {
        $userId = Auth::id();

        return new self(
            id: $setId ? $lesson->id : 0,
            title: $lesson->title,
            slug: $lesson->slug,
            content: $lesson->content,
            video_url: $lesson->video_url,
            published_at: $lesson->published_at,
            isSaved: $lesson->isSavedBy($userId),
            canUpdate: Gate::allows('update', $lesson),
            canDelete: Gate::allows('delete', $lesson)
        );
    }
}
