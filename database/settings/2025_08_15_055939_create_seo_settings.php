<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('SeoSettings.meta_titleTemplate');
        $this->migrator->add('SeoSettings.meta_description');
        $this->migrator->add('SeoSettings.meta_keywords');
        $this->migrator->add('SeoSettings.meta_canonicalEnabled');
        $this->migrator->add('SeoSettings.meta_robots');
        $this->migrator->add('SeoSettings.meta_custom');
        $this->migrator->add('SeoSettings.twitter_enabled');
        $this->migrator->add('SeoSettings.twitter_site');
        $this->migrator->add('SeoSettings.twitter_card');
        $this->migrator->add('SeoSettings.twitter_title');
        $this->migrator->add('SeoSettings.twitter_description');
        $this->migrator->add('SeoSettings.twitter_image');
        $this->migrator->add('SeoSettings.twitter_imageAlt');
        $this->migrator->add('SeoSettings.og_enabled');
        $this->migrator->add('SeoSettings.og_site');
        $this->migrator->add('SeoSettings.og_type');
        $this->migrator->add('SeoSettings.og_title');
        $this->migrator->add('SeoSettings.og_description');
        $this->migrator->add('SeoSettings.og_images');
        $this->migrator->add('SeoSettings.og_determiner');
        $this->migrator->add('SeoSettings.jld_enabled');
        $this->migrator->add('SeoSettings.jld_pretty');
        $this->migrator->add('SeoSettings.jld_type');
        $this->migrator->add('SeoSettings.jld_name');
        $this->migrator->add('SeoSettings.jld_description');
        $this->migrator->add('SeoSettings.jld_images');
        $this->migrator->add('SeoSettings.jld_custom');
        $this->migrator->add('SeoSettings.jld_placeOnGraph');
        $this->migrator->add('SeoSettings.sync_urlCanonical');
        $this->migrator->add('SeoSettings.sync_keywordsTags');
        $this->migrator->add('SeoSettings.extra_header');
        $this->migrator->add('SeoSettings.extra_footer');
    }
};
