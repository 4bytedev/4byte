<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserProfile;
use App\Observers\UserObserver;
use App\Observers\UserProfileObserver;
use App\Services\SettingsService;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Packages\React\Services\ReactService;
use Packages\Search\Services\SearchService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 6);

        register_shutdown_function(function () {
            if (memory_get_usage() > 100 * 1024 * 1024) {
                logger()->warning('High memory usage: ' . memory_get_usage());
            }
        });

        $this->loadObservers();
        $this->loadMacros();
        $this->configureSearch();
        $this->configureReact();

        try {
            $siteSettings = SettingsService::getSiteSettings();
            $seoSettings  = SettingsService::getSeoSettings();
            $this->loadSiteConfig($siteSettings);
            $this->loadSeoConfig($siteSettings, $seoSettings);
        } catch (\Throwable $th) {
            logger()->error('Service provider settings configuration error, please migrate and configure settings', ['e' => $th]);
        }
    }

    protected function loadSeoConfig(SiteSettings $siteSettings, SeoSettings $seoSettings): void
    {
        $generators = [
            'MetaGenerator' => [
                'title'            => $siteSettings->title,
                'titleTemplate'    => $seoSettings->meta_titleTemplate,
                'description'      => $seoSettings->meta_description,
                'keywords'         => $seoSettings->meta_keywords,
                'canonicalEnabled' => $seoSettings->meta_canonicalEnabled,
                'robots'           => $seoSettings->meta_robots,
                'custom'           => [$seoSettings->meta_custom],
            ],
            'TwitterGenerator' => [
                'enabled'     => $seoSettings->twitter_enabled,
                'site'        => $seoSettings->twitter_site,
                'card'        => $seoSettings->twitter_card,
                'title'       => $seoSettings->twitter_title,
                'description' => $seoSettings->twitter_description,
                'image'       => $seoSettings->twitter_image,
                'imageAlt'    => $seoSettings->twitter_imageAlt,
            ],
            'OpenGraphGenerator' => [
                'enabled'     => $seoSettings->og_enabled,
                'site'        => $seoSettings->og_site,
                'type'        => $seoSettings->og_type,
                'title'       => $seoSettings->og_title,
                'description' => $seoSettings->og_description,
                'images'      => $seoSettings->og_images,
                'determiner'  => $seoSettings->og_determiner,
            ],
            'JsonLdGenerator' => [
                'enabled'     => $seoSettings->jld_enabled,
                'pretty'      => $seoSettings->jld_pretty,
                'type'        => $seoSettings->jld_type,
                'name'        => $seoSettings->jld_name,
                'description' => $seoSettings->jld_description,
                'images'      => $seoSettings->jld_images,
                'custom'      => [$seoSettings->jld_custom],
            ],
        ];

        foreach ($generators as $generator => $values) {
            foreach ($values as $key => $value) {
                config(["honeystone-seo.generators.Honeystone\Seo\Generators\\{$generator}.{$key}" => $value]);
            }
        }
    }

    protected function loadSiteConfig(SiteSettings $siteSettings): void
    {
        if ($siteSettings->terms_and_conditions_url) {
            config([
                'filament-cookie-consent.privacy_policy_button.enabled' => true,
                'filament-cookie-consent.privacy_policy_button.href'    => $siteSettings->terms_and_conditions_url,
            ]);
        }
    }

    protected function loadObservers(): void
    {
        User::observe(UserObserver::class);
        UserProfile::observe(UserProfileObserver::class);
    }

    protected function loadMacros(): void
    {
        Builder::macro('existsOrFail', function ($message = '') {
            if (! $this->exists()) {
                throw new \Illuminate\Database\RecordNotFoundException($message);
            }

            return $this;
        });
    }

    protected function configureSearch(): void
    {
        SearchService::registerHandler(
            index: 'users',
            callback: fn ($hit) => app(\App\Services\UserService::class)->getData($hit['id']),
            searchableAttributes: ['name', 'username'],
            filterableAttributes: ['id'],
            sortableAttributes: ['created_at']
        );
    }

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: 'user',
            class: User::class,
            callback: fn ($slug) => app(\App\Services\UserService::class)->getId($slug)
        );
    }
}
