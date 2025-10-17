<?php

namespace App\Filament\Pages\Auth;

use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected ?SecuritySettings $securitySettings;

    public function __construct()
    {
        $this->securitySettings = SettingsService::getSecuritySettings();
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $user = \App\Models\User::where('email', $data['email'])->first();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getForms(): array
    {
        $schema = [
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
        ];
        if ($this->securitySettings->remember_me_enabled ?? true) {
            $schema[] = $this->getRememberFormComponent();
        }

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($schema)
                    ->statePath('data'),
            ),
        ];
    }
}
