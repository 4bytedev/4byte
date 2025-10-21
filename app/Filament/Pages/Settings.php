<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Settings as PartSettings;
use App\Jobs\GenerateSitemapJob;
use App\Services\SettingsService;
use App\Settings as BaseSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * @property Forms\Form $form
 */
class Settings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    /** @var array<string, mixed> */
    public array $siteSettings = [];

    /** @var array<string, mixed> */
    public array $seoSettings = [];

    /** @var array<string, mixed> */
    public array $securitySettings = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public function mount(): void
    {
        $this->siteSettings     = app(BaseSettings\SiteSettings::class)->toArray();
        $this->seoSettings      = app(BaseSettings\SeoSettings::class)->toArray();
        $this->securitySettings = app(BaseSettings\SecuritySettings::class)->toArray();
        $this->mutateFormDataBeforeFill();
    }

    public function save(): void
    {
        /** @var Forms\Form $form */
        $form = $this->form;

        $filledDatas = $form->getState();
        $saveDatas   = $this->mutateFormDataBeforeSave($filledDatas);
        $this->handleFileUploads($saveDatas);
        app(BaseSettings\SiteSettings::class)->fill($saveDatas['siteSettings'])->save();
        app(BaseSettings\SeoSettings::class)->fill($saveDatas['seoSettings'])->save();
        app(BaseSettings\SecuritySettings::class)->fill($saveDatas['securitySettings'])->save();
        SettingsService::updateSiteSettings($saveDatas['siteSettings']);
        SettingsService::updateSeoSettings($saveDatas['seoSettings']);
        SettingsService::updateSecuritySettings($saveDatas['securitySettings']);
        Notification::make()
            ->title(__('Settings successfully saved'))
            ->success()->send();
    }

    /**
     * @return array<int, Forms\Components\Actions\Action>
     */
    public function formActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label(__('Save'))
                ->action('save')
                ->visible(true),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function mutateFormDataBeforeSave(array $data): array
    {
        /** @var array<int, array{available_language: string}> $availableLanguages */
        $availableLanguages                          = $data['siteSettings']['available_languages'] ?? [];
        $data['siteSettings']['available_languages'] = collect($availableLanguages)
            ->pluck('available_language')
            ->toArray();

        /** @var array<int, array{allowed_email: string}> $allowedEmails */
        $allowedEmails                              = $data['securitySettings']['allowed_emails'] ?? [];
        $data['securitySettings']['allowed_emails'] = collect($allowedEmails)
            ->pluck('allowed_email')
            ->toArray();

        foreach (['light_logo', 'dark_logo', 'favicon'] as $logoField) {
            if (isset($data['siteSettings'][$logoField]) && is_array($data['siteSettings'][$logoField])) {
                $data['siteSettings'][$logoField] = $data['siteSettings'][$logoField][0] ?? null;
            }
        }
        if ($data['seoSettings']['meta_keywords']) {
            $data['seoSettings']['meta_keywords'] = explode(',', $data['seoSettings']['meta_keywords']);
        }

        return $data;
    }

    public function generateSitemap(): void
    {
        GenerateSitemapJob::dispatch(Auth::id());

        Notification::make()
            ->title(__('Sitemap generation queued'))
            ->success()
            ->send();
    }

    public function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('filament:optimize-clear');
        Cache::forget('widget-image-models');
        Cache::forget('chat-settings');
        Cache::forget('image-settings');
        Cache::forget('file-manager-settings');
        Cache::forget('chat-models');

        Notification::make()
            ->title(__('Cache successfully cleared'))
            ->success()
            ->send();
    }

    public function optimizeSite(): void
    {
        // Artisan::call('filament:optimize');
        Artisan::call('icons:cache');
        Artisan::call('config:cache');
        Artisan::call('event:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('optimize');

        Notification::make()
            ->title(__('Site successfully optimized'))
            ->success()
            ->send();
    }

    /**
     * @return array<int, Forms\Components\Tabs>
     */
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make()
                ->tabs([
                    PartSettings\SiteSettings::get(),
                    PartSettings\SeoSettings::get(),
                    PartSettings\SecuritySettings::get(),
                ]),

        ];
    }

    protected function mutateFormDataBeforeFill(): void
    {
        /** @var array<int, array{available_language: string}> $availableLanguages */
        $availableLanguages                        = $this->siteSettings['available_languages'] ?? [];
        $this->siteSettings['available_languages'] = collect($availableLanguages)
            ->map(fn ($ip) => ['available_language' => $ip])
            ->toArray();

        /** @var array<int, array{allowed_email: string}> $allowedEmails */
        $allowedEmails                            = $this->securitySettings['allowed_emails'] ?? [];
        $this->securitySettings['allowed_emails'] = collect($allowedEmails)
            ->map(fn ($lang) => ['allowed_email' => $lang])
            ->toArray();

        if ($this->siteSettings['light_logo']) {
            $this->siteSettings['light_logo'] = [$this->siteSettings['light_logo']];
        }
        if ($this->siteSettings['dark_logo']) {
            $this->siteSettings['dark_logo'] = [$this->siteSettings['dark_logo']];
        }
        if ($this->siteSettings['favicon']) {
            $this->siteSettings['favicon'] = [$this->siteSettings['favicon']];
        }
        if ($this->seoSettings['meta_keywords']) {
            $this->seoSettings['meta_keywords'] = implode(',', $this->seoSettings['meta_keywords']);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function handleFileUploads(array &$data): void
    {
        foreach (['light_logo', 'dark_logo', 'favicon'] as $field) {
            if (isset($data['siteSettings'][$field]) && $data['siteSettings'][$field] instanceof UploadedFile) {
                $old = app(BaseSettings\SiteSettings::class)->{$field};
                if ($old && Storage::exists($old)) {
                    Storage::delete($old);
                }

                $file                         = $data['siteSettings'][$field];
                $path                         = $file->store('site/images');
                $data['siteSettings'][$field] = $path;
            }
        }

        if (isset($data['seoSettings']['twitter_image']) && $data['seoSettings']['twitter_image'] instanceof UploadedFile) {
            $old = app(BaseSettings\SeoSettings::class)->twitter_image;
            if ($old && Storage::exists($old)) {
                Storage::delete($old);
            }

            $file                                 = $data['seoSettings']['twitter_image'];
            $path                                 = $file->store('site/images');
            $data['seoSettings']['twitter_image'] = $path;
        }

        if (isset($data['seoSettings']['og_images'])) {
            $oldImages = app(BaseSettings\SeoSettings::class)->og_images ?? [];
            $cond      = false;

            foreach ($data['seoSettings']['og_images'] as $new) {
                if ($new instanceof UploadedFile) {
                    $cond = true;
                }
            }

            if ($cond) {
                foreach ($oldImages as $old) {
                    if ($old && Storage::exists($old)) {
                        Storage::delete($old);
                    }
                }

                $storedPaths = [];
                foreach ($data['seoSettings']['og_images'] as $file) {
                    if ($file && $file instanceof UploadedFile) {
                        $storedPaths[] = $file->store('site/images');
                    }
                }
                $data['seoSettings']['og_images'] = $storedPaths;
            }
        }
        if (isset($data['seoSettings']['jld_images'])) {
            $oldImages = app(BaseSettings\SeoSettings::class)->jld_images ?? [];
            $cond      = false;

            foreach ($data['seoSettings']['jld_images'] as $new) {
                if ($new instanceof UploadedFile) {
                    $cond = true;
                }
            }

            if ($cond) {
                foreach ($oldImages as $old) {
                    if ($old && Storage::exists($old)) {
                        Storage::delete($old);
                    }
                }

                $storedPaths = [];
                foreach ($data['seoSettings']['jld_images'] as $file) {
                    if ($file) {
                        $storedPaths[] = $file->store('site/images');
                    }
                }

                $data['seoSettings']['jld_images'] = $storedPaths;
            }
        }
    }

    /**
     * @return array<int, \Filament\Pages\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Pages\Actions\Action::make('generate_sitemap')
                ->color('gray')
                // ->disabled(!$this->seoSettings['sitemap_enabled'])
                ->label(__('Generate Sitemap'))
                ->icon('heroicon-o-document')
                ->action(fn () => $this->generateSitemap())
                ->requiresConfirmation()
                ->successNotificationTitle(__('Sitemap successfully generated')),
            \Filament\Pages\Actions\Action::make('clear_cache')
                ->color('gray')
                ->label(__('Clear Cache'))
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(fn () => $this->clearCache())
                ->requiresConfirmation()
                ->successNotificationTitle(__('Cache successfully cleared')),
            \Filament\Pages\Actions\Action::make('optimize_site')
                ->color('gray')
                ->label(__('Optimize Site'))
                ->icon('heroicon-o-rocket-launch')
                ->action(fn () => $this->optimizeSite())
                ->requiresConfirmation()
                ->successNotificationTitle(__('Site successfully optimized')),
        ];
    }
}
