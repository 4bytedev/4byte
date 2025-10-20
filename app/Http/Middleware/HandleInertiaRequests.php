<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $siteSettings     = SettingsService::getSiteSettings();
        $securitySettings = SettingsService::getSecuritySettings();
        $user             = null;
        if ($request->user() !== null) {
            $user = [
                'name'     => $request->user()->name,
                'username' => $request->user()->username,
                'avatar'   => $request->user()->getAvatarImage(),
                'verified' => $request->user()->email_verified_at !== null,
            ];
        }

        return [
            ...parent::share($request),
            'site' => [
                'title' => $siteSettings->title,
                'logo'  => [
                    'light' => $siteSettings->light_logo ? $siteSettings->getLightLogoUrlAttribute() : null,
                    'dark'  => $siteSettings->dark_logo ? $siteSettings->getDarkLogoUrlAttribute() : null,
                ],
                // 'favicon' => $siteSettings->favicon ? $siteSettings->getFaviconUrlAttribute() : null,
                'pages' => [
                    'terms'   => $siteSettings->terms_and_conditions_url ?? null,
                    'privacy' => $siteSettings->privacy_policy_url ?? null,
                ],
                'settings' => [
                    'verification' => $securitySettings->email_verification_required,
                    'login'        => $securitySettings->login_enabled,
                    'register'     => $securitySettings->register_enabled,
                ],
                'i18n' => [
                    'languages' => $siteSettings->available_languages,
                    'default'   => $siteSettings->default_language,
                ],
            ],
            'account'    => $user,
            'csrf_token' => csrf_token(),
        ];
    }
}
