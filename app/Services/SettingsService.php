<?php

namespace App\Services;

use App\Settings\SecuritySettings;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;

class SettingsService
{
    protected static ?SiteSettings $siteSettings = null;

    protected static ?SeoSettings $seoSettings = null;

    protected static ?SecuritySettings $securitySettings = null;

    /**
     * Get site settings instance.
     */
    public static function getSiteSettings(): SiteSettings
    {
        if (self::$siteSettings === null) {
            self::$siteSettings = app(SiteSettings::class);
        }

        return self::$siteSettings;
    }

    /**
     * Get a specific field from site settings.
     *
     * @return mixed|null
     */
    public static function getSiteSettingsField(string $field): mixed
    {
        $settings = self::getSiteSettings();

        return $settings->$field ?? null;
    }

    /**
     * Update site settings fields.
     *
     * @param array<string, mixed> $data
     */
    public static function updateSiteSettings(array $data): void
    {
        $settings = self::getSiteSettings();
        foreach ($data as $key => $value) {
            if (property_exists($settings, $key)) {
                $settings->$key = $value;
            }
        }
        $settings->save();
        self::$siteSettings = $settings;
    }

    /**
     * Get SEO settings instance.
     */
    public static function getSeoSettings(): SeoSettings
    {
        if (self::$seoSettings === null) {
            self::$seoSettings = app(SeoSettings::class);
        }

        return self::$seoSettings;
    }

    /**
     * Get a specific field from SEO settings.
     *
     * @return mixed|null
     */
    public static function getSeoSettingsField(string $field): mixed
    {
        $settings = self::getSeoSettings();

        return $settings->$field ?? null;
    }

    /**
     * Update SEO settings fields.
     *
     * @param array<string, mixed> $data
     */
    public static function updateSeoSettings(array $data): void
    {
        $settings = self::getSeoSettings();
        foreach ($data as $key => $value) {
            if (property_exists($settings, $key)) {
                $settings->$key = $value;
            }
        }
        $settings->save();
        self::$seoSettings = $settings;
    }

    /**
     * Get security settings instance.
     */
    public static function getSecuritySettings(): SecuritySettings
    {
        if (self::$securitySettings === null) {
            self::$securitySettings = app(SecuritySettings::class);
        }

        return self::$securitySettings;
    }

    /**
     * Get a specific field from security settings.
     *
     * @return mixed|null
     */
    public static function getSecuritySettingsField(string $field): mixed
    {
        $settings = self::getSecuritySettings();

        return $settings->$field ?? null;
    }

    /**
     * Update security settings fields.
     *
     * @param array<string, mixed> $data
     */
    public static function updateSecuritySettings(array $data): void
    {
        $settings = self::getSecuritySettings();
        foreach ($data as $key => $value) {
            if (property_exists($settings, $key)) {
                $settings->$key = $value;
            }
        }
        $settings->save();
        self::$securitySettings = $settings;
    }
}
