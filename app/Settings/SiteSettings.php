<?php

namespace App\Settings;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public ?string $title;

    public ?string $panel_url = 'panel';

    public ?string $light_logo;

    public ?string $dark_logo;

    public ?string $favicon;

    public ?int $default_role;

    /** @var array<int, string>|null */
    public ?array $available_languages;

    public ?string $default_language;

    public ?string $terms_and_conditions_url = '/terms-and-conditions';

    public ?string $privacy_policy_url = '/privacy-policy';

    public ?string $theme_mode = 'system';

    public ?bool $dark_mode_enabled = true;

    public ?bool $spa_enabled = true;

    public static function group(): string
    {
        return 'SiteSettings';
    }

    public function getLightLogoUrlAttribute(): ?string
    {
        if (! $this->light_logo) {
            return null;
        }

        return Storage::url($this->light_logo);
    }

    public function getDarkLogoUrlAttribute(): ?string
    {
        if (! $this->dark_logo) {
            return null;
        }

        return Storage::url($this->dark_logo);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if (! $this->favicon) {
            return null;
        }

        return Storage::url($this->favicon);
    }
}
