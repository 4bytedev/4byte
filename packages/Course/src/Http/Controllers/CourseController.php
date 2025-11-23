<?php

namespace Packages\Course\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Course\Services\CourseService;

class CourseController extends Controller
{
    protected CourseService $courseService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->courseService = app(CourseService::class);
        $this->seoService    = app(SeoService::class);
    }

    /**
     * Display a course detail page.
     */
    public function view(Request $request): Response
    {
        $slug       = $request->route('slug');
        $courseId   = $this->courseService->getId($slug);
        $course     = $this->courseService->getData($courseId);
        $cirriculum = $this->courseService->getCirriculum($courseId);

        return Inertia::render('Course/Detail', [
            'course'     => $course,
            'cirriculum' => $cirriculum,
        ])->withViewData(['seo' => $this->seoService->getCourseSEO($course, $course->user)]);
    }

    /**
     * Display a lesson detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function page(Request $request): Response
    {
        $slug       = $request->route('slug');
        $page       = $request->route('page');
        $courseId   = $this->courseService->getId($slug);
        $lessonId   = $this->courseService->getLessonId($courseId, $page);
        $lesson     = $this->courseService->getLesson($courseId, $lessonId);
        $cirriculum = $this->courseService->getCirriculum($courseId);

        return Inertia::render('Course/Page', [
            'course'     => $slug,
            'lesson'     => $lesson,
            'cirriculum' => $cirriculum,
        ])->withViewData(['seo' => $this->seoService->getCourseLessonSEO($lesson, $slug)]);
    }
}
