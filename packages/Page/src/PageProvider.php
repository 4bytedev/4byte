<?php

namespace Packages\Page;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Page\Console\Commands\SchedulePageCommand;
use Packages\Page\Events\PagePublishedEvent;
use Packages\Page\Listeners\PagePublishedListener;
use Packages\Page\Models\Page;
use Packages\Page\Observers\PageObserver;
use Packages\Page\Policies\PagePolicy;
use Packages\Search\Services\SearchService;

class PageProvider extends ServiceProvider
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
        Gate::policy(Page::class, PagePolicy::class);
    }

    public function loadObservers(): void
    {
        Page::observe(PageObserver::class);
    }

    public function loadEvents(): void
    {
        Event::listen(PagePublishedEvent::class, PagePublishedListener::class);
    }

    public function loadCommands(): void
    {
        $this->commands([
            SchedulePageCommand::class,
        ]);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Page\Http\Controllers')
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
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/page'),
            ], 'page-seeders');
        }
    }

    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'page');
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

    protected function configureSearch()
    {
        SearchService::registerHandler(
            index: "pages", 
            callback: fn($hit) => app(\Packages\Page\Services\PageService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
    }
}
