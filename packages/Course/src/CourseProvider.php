<?php

namespace Packages\Course;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Course\Console\Commands\ScheduleCourseCommand;
use Packages\Course\Console\Commands\ScheduleLessonCommand;
use Packages\Course\Events\CoursePublishedEvent;
use Packages\Course\Events\LessonPublishedEvent;
use Packages\Course\Listeners\CoursePublishedListener;
use Packages\Course\Listeners\LessonPublishedListener;
use Packages\Course\Models\Course;
use Packages\Course\Models\CourseChapter;
use Packages\Course\Models\CourseLesson;
use Packages\Course\Observers\CourseObserver;
use Packages\Course\Policies\CourseChapterPolicy;
use Packages\Course\Policies\CourseLessonPolicy;
use Packages\Course\Policies\CoursePolicy;
use Packages\React\Services\ReactService;
use Packages\Search\Services\SearchService;

class CourseProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadEvents();
        $this->loadCommands();
        $this->loadRoutes();
        $this->loadFactories();
        $this->loadSeeders();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->configureSearch();
    }

    public function loadPolicies(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(CourseChapter::class, CourseChapterPolicy::class);
        Gate::policy(CourseLesson::class, CourseLessonPolicy::class);
    }

    public function loadObservers(): void
    {
        Course::observe(CourseObserver::class);
    }

    public function loadEvents(): void
    {
        Event::listen(CoursePublishedEvent::class, CoursePublishedListener::class);
        Event::listen(LessonPublishedEvent::class, LessonPublishedListener::class);
    }

    public function loadCommands(): void
    {
        $this->commands([
            ScheduleCourseCommand::class,
            ScheduleLessonCommand::class,
        ]);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Course\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
    }

    protected function loadSeeders(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/course'),
            ], 'seeders');
        }
    }

    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'course');
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations/'),
            ], 'migrations');
        }
    }

    protected function configureSearch(): void
    {
        SearchService::registerHandler(
            index: 'courses',
            callback: fn ($hit) => app(Services\CourseService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
        SearchService::registerHandler(
            index: 'lessons',
            callback: fn ($hit) => app(Services\CourseService::class)->getLessonByChapter($hit['chapter_id'], $hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id', 'chapter_id'],
            sortableAttributes: ['updated_at']
        );
    }

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: "course",
            class: Course::class, 
            callback: fn ($slug) => app(Services\CourseService::class)->getId($slug)
        );
    }
}
