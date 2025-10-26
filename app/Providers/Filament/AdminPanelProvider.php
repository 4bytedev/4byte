<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EmailVerification;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\ResetPassword;
use App\Filament\Pages\MyProfile;
use App\Filament\Resources\ArticleResource\Widgets\ArticleStatsOverview;
use App\Filament\Resources\ArticleResource\Widgets\RecentArticles;
use App\Filament\Resources\CommentResource\Widgets\CommentStatsOverview;
use App\Filament\Resources\EntryResource\Widgets\EntryStatsOverview;
use App\Filament\Resources\EntryResource\Widgets\RecentEntries;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use MarcoGermani87\FilamentCookieConsent\FilamentCookieConsent;
use Rmsramos\Activitylog\ActivitylogPlugin;

class AdminPanelProvider extends PanelProvider
{
    protected SiteSettings $siteSettings;

    protected SecuritySettings $securitySettings;

    protected SeoSettings $seoSettings;

    public function __construct()
    {
        $this->siteSettings     = SettingsService::getSiteSettings();
        $this->securitySettings = SettingsService::getSecuritySettings();
        $this->seoSettings      = SettingsService::getSeoSettings();
    }

    public function boot(): void
    {
        try {
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
                $switch
                    ->locales($this->siteSettings->available_languages ?? []);
            });
        } catch (\Throwable $th) {
            logger()->error('Admin panel provider settings configuration error, please migrate and configure settings', ['e' => $th]);
        }
    }

    public function panel(Panel $panel): Panel
    {
        try {
            return $panel
                ->default()
                ->id('admin')
                ->path($this->siteSettings->panel_url ?? 'admin')
                ->login()

                ->brandName($this->siteSettings->title ?? config('app.name'))
                ->when(isset($this->siteSettings->light_logo), fn ($panel) => $panel->brandLogo(Storage::url($this->siteSettings->light_logo)))
                ->when(isset($this->siteSettings->dark_logo), fn ($panel) => $panel->darkModeBrandLogo(Storage::url($this->siteSettings->dark_logo)))
                ->when(isset($this->siteSettings->favicon), fn ($panel) => $panel->favicon(Storage::url($this->siteSettings->favicon)))

                ->when($this->securitySettings->login_enabled ?? true, fn ($panel) => $panel->login(Login::class))
                ->when($this->securitySettings->register_enabled ?? true, fn ($panel) => $panel->registration(Register::class))
                ->when($this->securitySettings->password_reset_enabled ?? true, fn ($panel) => $panel->passwordReset(ResetPassword::class))
                ->when($this->securitySettings->email_verification_required ?? false, fn ($panel) => $panel->emailVerification(EmailVerification::class))

                ->when($this->siteSettings->spa_enabled ?? false, fn ($panel) => $panel->spa())

                ->broadcasting(false)
                ->databaseNotifications()

                ->colors([
                    'primary' => Color::Amber,
                ])
                ->defaultThemeMode($this->siteSettings->theme_mode ?? $this->siteSettings->theme_mode === 'dark' ? ThemeMode::Dark :
                    ($this->siteSettings->theme_mode ?? $this->siteSettings->theme_mode === 'light' ? ThemeMode::Light : ThemeMode::System))
                ->darkMode($this->siteSettings->dark_mode_enabled ?? true)

                ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
                ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
                ->pages([])
                ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
                ->widgets([
                    ArticleStatsOverview::class,
                    EntryStatsOverview::class,
                    CommentStatsOverview::class,
                    RecentArticles::class,
                    RecentEntries::class,
                ])
                ->middleware([
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                    AuthenticateSession::class,
                    ShareErrorsFromSession::class,
                    VerifyCsrfToken::class,
                    SubstituteBindings::class,
                    DisableBladeIconComponents::class,
                    DispatchServingFilamentEvent::class,
                ])
                ->authMiddleware([
                    Authenticate::class,
                ])
                ->plugins($this->getPlugins());
        } catch (\Throwable $th) {
            return $panel->default()->path('admin')->login();
        }
    }

    /**
     * @return array<int, Plugin>
     */
    protected function getPlugins(): array
    {
        return [
            BreezyCore::make()
                ->customMyProfilePage(MyProfile::class)
                ->enableTwoFactorAuthentication($this->securitySettings->two_factor_authentication_enabled ?? true)
                ->enableBrowserSessions()
                ->myProfile(
                    slug: 'account',
                    hasAvatars: true,
                    shouldRegisterNavigation: false,
                    shouldRegisterUserMenu: true,
                )
                ->avatarUploadComponent(fn () => SpatieMediaLibraryFileUpload::make('avatar')->image()->imageEditor()->avatar()->circleCropper()->disableLabel()->collection('avatar')),
            FilamentCookieConsent::make(),
            FilamentShieldPlugin::make(),
            ActivitylogPlugin::make(),
        ];
    }
}
