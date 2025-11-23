<?php

namespace Packages\Article;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Article\Console\Commands\ScheduleArticleCommand;
use Packages\Article\Events\ArticlePublishedEvent;
use Packages\Article\Listeners\ArticlePublishedListener;
use Packages\Article\Models\Article;
use Packages\Article\Observers\ArticleObserver;
use Packages\Article\Policies\ArticlePolicy;
use Packages\React\Services\ReactService;
use Packages\Search\Services\SearchService;

class ArticleProvider extends ServiceProvider
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
        $this->configureReact();
    }

    public function loadPolicies(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
    }

    public function loadObservers(): void
    {
        Article::observe(ArticleObserver::class);
    }

    public function loadEvents(): void
    {
        Event::listen(ArticlePublishedEvent::class, ArticlePublishedListener::class);
    }

    public function loadCommands(): void
    {
        $this->commands([
            ScheduleArticleCommand::class,
        ]);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Article\Http\Controllers')
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
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/article'),
            ], 'article-seeders');
        }
    }

    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'article');
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
            index: 'articles',
            callback: fn ($hit) => app(Services\ArticleService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
    }

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: "article",
            class: Article::class, 
            callback: fn ($slug) => app(Services\ArticleService::class)->getId($slug)
        );
    }
}
