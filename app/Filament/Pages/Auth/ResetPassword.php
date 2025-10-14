<?php

namespace App\Filament\Pages\Auth;

use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BasePasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;

class ResetPassword extends BasePasswordReset
{
    protected ?SecuritySettings $securitySettings;

    public function __construct()
    {
        $this->securitySettings = SettingsService::getSecuritySettings();
    }

    public function request(): void
    {
        try {
            $this->rateLimit($this->securitySettings->max_reset_password_attempts, $this->securitySettings->max_reset_password_attempts_seconds);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $data = $this->form->getState();

        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            $data,
            function (CanResetPassword|Notifiable $user, string $token): void {
                $notification = app(ResetPasswordNotification::class, ['token' => $token]);
                $notification->url = Filament::getResetPasswordUrl($token, $user);

                $user->notify($notification);
            },
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__($status))
            ->success()
            ->send();

        $this->form->fill();
    }

    protected function getForms(): array
    {
        $schema = [
            $this->getEmailFormComponent(),
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
