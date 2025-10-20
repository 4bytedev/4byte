<?php

namespace App\Filament\Pages\Auth;

use App\Services\SettingsService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseEmailVerification;

class EmailVerification extends BaseEmailVerification
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function resendNotificationAction(): Action
    {
        $securitySettings = SettingsService::getSecuritySettings();

        return Action::make('resendNotification')
            ->link()
            ->label(__('filament-panels::pages/auth/email-verification/email-verification-prompt.actions.resend_notification.label') . '.')
            ->action(function () use ($securitySettings): void {
                try {
                    $this->rateLimit($securitySettings->max_email_verification_attempts, $securitySettings->max_email_verification_attempts_seconds);
                } catch (TooManyRequestsException $exception) {
                    $this->getRateLimitedNotification($exception)?->send();

                    return;
                }

                $this->sendEmailVerificationNotification($this->getVerifiable());

                Notification::make()
                    ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resent.title'))
                    ->success()
                    ->send();
            });
    }
}
