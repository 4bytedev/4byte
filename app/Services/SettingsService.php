<?php

namespace App\Services;

use App\Settings\SecuritySettings;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;

class SettingsService
{
    protected static $siteSettings = null;

    protected static $seoSettings = null;

    protected static $securitySettings = null;

    public static function getSiteSettings(): SiteSettings
    {
        if (self::$siteSettings === null) {
            self::$siteSettings = app(SiteSettings::class);
        }

        return self::$siteSettings;
    }

    public static function getSiteSettingsField(string $field)
    {
        $settings = self::getSiteSettings();

        return $settings->$field ?? null;
    }

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

    public static function getSeoSettings(): SeoSettings
    {
        if (self::$seoSettings === null) {
            self::$seoSettings = app(SeoSettings::class);
        }

        return self::$seoSettings;
    }

    public static function getSeoSettingsField(string $field)
    {
        $settings = self::getSeoSettings();

        return $settings->$field ?? null;
    }

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

    public static function getSecuritySettings(): SecuritySettings
    {
        if (self::$securitySettings === null) {
            self::$securitySettings = app(SecuritySettings::class);
        }

        return self::$securitySettings;
    }

    public static function getSecuritySettingsField(string $field)
    {
        $settings = self::getSecuritySettings();

        return $settings->$field ?? null;
    }

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
