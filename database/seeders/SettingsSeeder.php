<?php

namespace Database\Seeders;

use App\Settings\SecuritySettings;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = app(SiteSettings::class);

        $settings->title                    = 'My Laravel App';
        $settings->panel_url                = 'admin';
        $settings->light_logo               = null;
        $settings->dark_logo                = null;
        $settings->favicon                  = null;
        $settings->default_role             = 2; // örnek role_id
        $settings->available_languages      = ['en', 'tr'];
        $settings->default_language         = 'en';
        $settings->terms_and_conditions_url = 'page/terms-and-conditions';
        $settings->privacy_policy_url       = 'page/privacy-policy';
        $settings->theme_mode               = 'system';
        $settings->dark_mode_enabled        = true;
        $settings->spa_enabled              = true;

        $settings->save();

        $settings = app(SeoSettings::class);

        // Meta
        $settings->meta_titleTemplate    = '%title% | My Laravel App';
        $settings->meta_description      = 'This is a default SEO description for my Laravel application.';
        $settings->meta_keywords         = ['laravel', 'seo', 'app'];
        $settings->meta_canonicalEnabled = true;
        $settings->meta_robots           = ['index', 'follow'];
        $settings->meta_custom           = [
            'author'    => 'My Company',
            'copyright' => '2025 My Company',
        ];

        // Twitter
        $settings->twitter_enabled     = false;
        $settings->twitter_site        = '@mycompany';
        $settings->twitter_card        = 'summary_large_image';
        $settings->twitter_title       = 'Welcome to My Laravel App';
        $settings->twitter_description = 'This is the default twitter description.';
        $settings->twitter_image       = null;
        $settings->twitter_imageAlt    = 'My Laravel App Banner';

        // Open Graph
        $settings->og_enabled     = true;
        $settings->og_site        = 'My Laravel App';
        $settings->og_type        = 'website';
        $settings->og_title       = 'Welcome to My Laravel App';
        $settings->og_description = 'This is the default Open Graph description.';
        $settings->og_images      = [];
        $settings->og_determiner  = 'the';

        // JSON-LD
        $settings->jld_enabled     = true;
        $settings->jld_pretty      = true;
        $settings->jld_type        = 'WebSite';
        $settings->jld_name        = 'My Laravel App';
        $settings->jld_description = 'This is the default JSON-LD description.';
        $settings->jld_images      = [];
        $settings->jld_custom      = [
            'publisher' => 'My Company',
        ];
        $settings->jld_placeOnGraph = true;

        // Sync
        $settings->sync_urlCanonical = true;
        $settings->sync_keywordsTags = true;

        // Extra
        $settings->extra_header             = '<!-- Custom header scripts -->';
        $settings->extra_footer             = '<!-- Custom footer scripts -->';
        $settings->google_analytics_id      = null;
        $settings->google_search_console_id = null;
        $settings->bing_webmaster_tools_id  = null;

        $settings->save();

        $settings = app(SecuritySettings::class);

        $settings->max_login_attempts         = 5;
        $settings->max_login_attempts_seconds = 60;

        $settings->max_register_attempts         = 2;
        $settings->max_register_attempts_seconds = 60;

        $settings->max_reset_password_attempts         = 2;
        $settings->max_reset_password_attempts_seconds = 60;

        $settings->max_email_verification_attempts         = 2;
        $settings->max_email_verification_attempts_seconds = 60;

        $settings->login_enabled                     = true;
        $settings->register_enabled                  = true;
        $settings->password_reset_enabled            = true;
        $settings->two_factor_authentication_enabled = true;

        $settings->captcha_login_enabled              = false;
        $settings->captcha_register_enabled           = false;
        $settings->captcha_reset_password_enabled     = false;
        $settings->captcha_email_verification_enabled = false;

        $settings->remember_me_enabled         = true;
        $settings->force_ssl                   = true;
        $settings->under_maintenance           = false;
        $settings->email_verification_required = false;

        $settings->allowed_emails = ['@gmail.com', '@hotmail.com'];

        $settings->save();
    }
}
