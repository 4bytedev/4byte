<?php

namespace Packages\React;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\React\Events\FollowedEvent;
use Packages\React\Listeners\FollowedListener;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Follow;
use Packages\React\Models\Like;
use Packages\React\Models\Save;
use Packages\React\Observers\CommentObserver;
use Packages\React\Observers\DislikeObserver;
use Packages\React\Observers\FollowObserver;
use Packages\React\Observers\LikeObserver;
use Packages\React\Observers\SaveObserver;
use Packages\React\Policies\CommentPolicy;
use Packages\React\Policies\DislikePolicy;
use Packages\React\Policies\FollowPolicy;
use Packages\React\Policies\LikePolicy;
use Packages\React\Policies\SavePolicy;
use Packages\React\Services\ReactService;

class ReactProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadRoutes();
        $this->loadEvents();
        $this->loadFactories();
        $this->loadMigrations();
        $this->configureReact();
    }

    public function loadPolicies(): void
    {
        Gate::policy(Like::class, LikePolicy::class);
        Gate::policy(Dislike::class, DislikePolicy::class);
        Gate::policy(Save::class, SavePolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Follow::class, FollowPolicy::class);
    }

    public function loadObservers(): void
    {
        Like::observe(LikeObserver::class);
        Dislike::observe(DislikeObserver::class);
        Save::observe(SaveObserver::class);
        Comment::observe(CommentObserver::class);
        Follow::observe(FollowObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\React\Http\Controllers')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function loadEvents(): void
    {
        Event::listen(FollowedEvent::class, FollowedListener::class);
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
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

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: "comment",
            class: Comment::class, 
            callback: fn ($slug) => $slug
        );
    }
}
