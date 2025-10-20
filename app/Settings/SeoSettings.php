<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public ?string $meta_titleTemplate;

    public ?string $meta_description;

    /** @var array<int, string>|null */
    public ?array $meta_keywords;

    public ?bool $meta_canonicalEnabled;

    /** @var array<int, string>|null */
    public ?array $meta_robots;

    /** @var array<string, string>|null */
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

    /** @var array<int, string>|null */
    public ?array $og_images;

    public ?string $og_determiner;

    public ?bool $jld_enabled;

    public ?bool $jld_pretty;

    public ?string $jld_type;

    public ?string $jld_name;

    public ?string $jld_description;

    /** @var array<int, string>|null */
    public ?array $jld_images;

    /** @var array<string, string>|null */
    public ?array $jld_custom;

    public ?bool $jld_placeOnGraph;

    public ?bool $sync_urlCanonical;

    public ?bool $sync_keywordsTags;

    public ?string $extra_header;

    public ?string $extra_footer;

    public static function group(): string
    {
        return 'SeoSettings';
    }
}
