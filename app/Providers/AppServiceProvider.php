<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserProfile;
use App\Observers\BannerObserver;
use App\Observers\UserObserver;
use App\Observers\UserProfileObserver;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Kenepa\Banner\Models\Banner;

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
                logger()->warning('High memory usage: '.memory_get_usage());
            }
        });

        User::observe(UserObserver::class);
        UserProfile::observe(UserProfileObserver::class);
        Banner::observe(BannerObserver::class);

        try {
            $siteSettings = SettingsService::getSiteSettings();
            $seoSettings = SettingsService::getSeoSettings();
            if ($siteSettings->terms_and_conditions_url) {
                config([
                    'filament-cookie-consent.privacy_policy_button.enabled' => true,
                    'filament-cookie-consent.privacy_policy_button.href' => $siteSettings->terms_and_conditions_url,
                ]);

                config([
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.title' => $siteSettings->title,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.titleTemplate' => $seoSettings->meta_titleTemplate,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.description' => $seoSettings->meta_description,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.keywords' => $seoSettings->meta_keywords,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.canonicalEnabled' => $seoSettings->meta_canonicalEnabled,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.robots' => $seoSettings->meta_robots,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\MetaGenerator.custom' => [$seoSettings->meta_custom],

                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.enabled' => $seoSettings->twitter_enabled,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.site' => $seoSettings->twitter_site,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.card' => $seoSettings->twitter_card,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.title' => $seoSettings->twitter_title,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.description' => $seoSettings->twitter_description,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.image' => $seoSettings->twitter_image,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\TwitterGenerator.imageAlt' => $seoSettings->twitter_imageAlt,

                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.enabled' => $seoSettings->og_enabled,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.site' => $seoSettings->og_site,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.type' => $seoSettings->og_type,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.title' => $seoSettings->og_title,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.description' => $seoSettings->og_description,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.images' => $seoSettings->og_images,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\OpenGraphGenerator.determiner' => $seoSettings->og_determiner,

                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.enabled' => $seoSettings->jld_enabled,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.pretty' => $seoSettings->jld_pretty,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.type' => $seoSettings->jld_type,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.name' => $seoSettings->jld_name,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.description' => $seoSettings->jld_description,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.images' => $seoSettings->jld_images,
                    'honeystone-seo.generators.Honeystone\Seo\Generators\JsonLdGenerator.custom' => [$seoSettings->jld_custom],
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error('Service provider settings configuration error, please migrate and configure settings', ['e' => $th]);
        }
    }
}
