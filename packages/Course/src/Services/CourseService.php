<?php

namespace Packages\Course\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Course\Data\CourseData;
use Packages\Course\Data\CourseLessonData;
use Packages\Course\Models\Course;
use Packages\Course\Models\CourseChapter;
use Packages\Course\Models\CourseLesson;

class CourseService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    /**
     * Retrieve course data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $courseId): CourseData
    {
        $course = Cache::rememberForever("course:{$courseId}", function () use ($courseId) {
            return Course::query()
                ->where('status', 'PUBLISHED')
                ->with(['categories:id,name,slug', 'tags:id,name,slug'])
                ->select(['id', 'title', 'slug', 'difficulty', 'excerpt', 'content', 'user_id', 'published_at'])
                ->findOrFail($courseId);
        });

        $user = $this->userService->getData($course->user_id);

        return CourseData::fromModel($course, $user);
    }

    /**
     * Retrieve the ID of a course by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("course:{$slug}:id", function () use ($slug) {
            return Course::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Get course cirriculum data by course ID.
     *
     * @return array<int, array{
     *     id: int,
     *     title: string,
     *     course_id: int,
     *     lessons: array<int, array{
     *         id: int,
     *         title: string,
     *         slug: string,
     *         chapter_id: int
     *     }>
     * }>
     */
    public function getCirriculum(int $courseId): array
    {
        return Cache::rememberForever("course:{$courseId}:cirriculum", function () use ($courseId) {
            return CourseChapter::select('id', 'title', 'course_id')
                ->where('course_id', $courseId)
                ->with([
                    'lessons' => function ($q) {
                        $q->select('id', 'title', 'slug', 'chapter_id');
                    },
                ])
                ->get()
                ->toArray();
        });
    }

    /**
     * Retrieve lesson data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getLesson(int $courseId, string $page): CourseLessonData
    {
        $lesson = Cache::rememberForever("course:{$courseId}:lesson:{$page}", function () use ($page) {
            return CourseLesson::query()
                ->where('status', 'PUBLISHED')
                ->where('slug', $page)
                ->select(['id', 'title', 'slug', 'content', 'video_url', 'published_at', 'user_id'])
                ->firstOrFail();
        });

        return CourseLessonData::fromModel($lesson);
    }
}
