<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public ?string $meta_titleTemplate;

    public ?string $meta_description;

    public ?array $meta_keywords;

    public ?bool $meta_canonicalEnabled;

    public ?array $meta_robots;

    public ?array $meta_custom;

    public ?bool $twitter_enabled;

    public ?string $twitter_site;

    public ?string $twitter_card;

    public ?string $twitter_title;

    public ?string $twitter_description;

    public ?string $twitter_image;

    public ?string $twitter_imageAlt;

    public ?bool $og_enabled;

    public ?string $og_site;

    public ?string $og_type;

    public ?string $og_title;

    public ?string $og_description;

    public ?array $og_images;

    public ?string $og_determiner;

    public ?bool $jld_enabled;

    public ?bool $jld_pretty;

    public ?string $jld_type;

    public ?string $jld_name;

    public ?string $jld_description;

    public ?array $jld_images;

    public ?array $jld_custom;

    public ?bool $jld_placeOnGraph;

    public ?bool $sync_urlCanonical;

    public ?bool $sync_keywordsTags;

    public ?string $extra_header;

    public ?string $extra_footer;

    public ?string $google_analytics_id;

    public ?string $google_search_console_id;

    public ?string $bing_webmaster_tools_id;

    protected $casts = [
        'meta_keywords' => 'array',
        'meta_robots' => 'array',
        'meta_custom' => 'array',

        'twitter_enabled' => 'boolean',

        'og_enabled' => 'boolean',
        'og_images' => 'array',

        'jld_enabled' => 'boolean',
        'jld_pretty' => 'boolean',
        'jld_images' => 'array',
        'jld_custom' => 'array',
        'jld_placeOnGraph' => 'boolean',

        'sync_urlCanonical' => 'boolean',
        'sync_keywordsTags' => 'boolean',
    ];

    public static function group(): string
    {
        return 'SeoSettings';
    }
}
