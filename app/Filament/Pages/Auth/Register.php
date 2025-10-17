<?php

namespace App\Filament\Pages\Auth;

use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected ?SecuritySettings $securitySettings;

    public function __construct()
    {
        $this->securitySettings = SettingsService::getSecuritySettings();
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit($this->securitySettings->max_register_attempts, $this->securitySettings->max_register_attempts_seconds);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $securitySettings = $this->securitySettings;

        $user = $this->wrapInDatabaseTransaction(function () use ($securitySettings) {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            // $data = $this->mutateFormDataBeforeRegister($data);

            if (isset($securitySettings->allowed_emails) && $securitySettings->allowed_emails) {
                $allowed = false;
                foreach ($securitySettings->allowed_emails as $allowedEmail) {
                    if (str_contains($data['email'], $allowedEmail)) {
                        $allowed = true;
                        break;
                    }
                }

                if (! $allowed) {
                    return Notification::make()
                        ->title(__('auth.bad_email_extension'))->danger();
                }
            }

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        if ($this->securitySettings->email_verification_required) {
            $this->sendEmailVerificationNotification($user);
        }

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    protected function getForms(): array
    {
        $schema = [
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ];

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($schema)
                    ->statePath('data'),
            ),
        ];
    }
}
