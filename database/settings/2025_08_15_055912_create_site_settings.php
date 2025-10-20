<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('SiteSettings.title');
        $this->migrator->add('SiteSettings.panel_url');
        $this->migrator->add('SiteSettings.light_logo');
        $this->migrator->add('SiteSettings.dark_logo');
        $this->migrator->add('SiteSettings.favicon');
        $this->migrator->add('SiteSettings.default_role');
        $this->migrator->add('SiteSettings.available_languages');
        $this->migrator->add('SiteSettings.default_language');
        $this->migrator->add('SiteSettings.terms_and_conditions_url');
        $this->migrator->add('SiteSettings.privacy_policy_url');
        $this->migrator->add('SiteSettings.theme_mode');
        $this->migrator->add('SiteSettings.dark_mode_enabled');
        $this->migrator->add('SiteSettings.spa_enabled');
    }
};
