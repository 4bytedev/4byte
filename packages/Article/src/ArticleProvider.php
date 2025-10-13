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
use Packages\Article\Models\ArticleComment;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Article\Models\ArticleDislike;
use Packages\Article\Models\ArticleLike;
use Packages\Article\Models\ArticleSave;
use Packages\Article\Observers\ArticleCommentLikeObserver;
use Packages\Article\Observers\ArticleDislikeObserver;
use Packages\Article\Observers\ArticleLikeObserver;
use Packages\Article\Observers\ArticleObserver;
use Packages\Article\Observers\ArticleSaveObserver;
use Packages\Article\Policies\ArticleCommentLikePolicy;
use Packages\Article\Policies\ArticleCommentPolicy;
use Packages\Article\Policies\ArticleDislikePolicy;
use Packages\Article\Policies\ArticleLikePolicy;
use Packages\Article\Policies\ArticlePolicy;
use Packages\Article\Policies\ArticleSavePolicy;

class ArticleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

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
    }

    public function loadPolicies(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(ArticleComment::class, ArticleCommentPolicy::class);
        Gate::policy(ArticleCommentLike::class, ArticleCommentLikePolicy::class);
        Gate::policy(ArticleDislike::class, ArticleDislikePolicy::class);
        Gate::policy(ArticleLike::class, ArticleLikePolicy::class);
        Gate::policy(ArticleSave::class, ArticleSavePolicy::class);
    }

    public function loadObservers(): void
    {
        Article::observe(ArticleObserver::class);
        ArticleLike::observe(ArticleLikeObserver::class);
        ArticleDislike::observe(ArticleDislikeObserver::class);
        ArticleCommentLike::observe(ArticleCommentLikeObserver::class);
        // ArticleSave::observe(ArticleSaveObserver::class);
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
            ->group(__DIR__.'/../routes/web.php');
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__.'/../database/factories');
    }

    protected function loadSeeders(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/seeders' => database_path('seeders/packages/article'),
            ], 'article-seeders');
        }
    }

    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'article');
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations/'),
            ], 'migrations');
        }
    }
}
