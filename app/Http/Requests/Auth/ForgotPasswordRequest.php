<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordRequest extends FormRequest
{
    protected SecuritySettings $securitySettings;

    public function __construct()
    {
        $this->securitySettings = SettingsService::getSecuritySettings();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Attempt to forgot password the request's credentials.
     *
     * @throws ValidationException
     */
    public function forgotPassword(): JsonResponse
    {
        $this->ensureForgotPasswordEnabled();
        $this->ensureIsNotRateLimited();

        Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            $this->only('email'),
            function (User $user, string $token): void {
                $notification      = app(ResetPassword::class, ['token' => $token]);
                $notification->url = URL::signedRoute(
                    'auth.reset-password.view',
                    [
                        'email' => $user->getEmailForPasswordReset(),
                        'token' => $token,
                    ],
                );

                $user->notify($notification);

                event(new PasswordResetLinkSent($user));
            },
        );

        return response()->json(['message' => 'Email successfully sended'], 200);
    }

    /**
     * Ensure the password reset request is allowed.
     *
     * @throws ValidationException
     */
    public function ensureForgotPasswordEnabled(): void
    {
        if (! $this->securitySettings->password_reset_enabled) {
            throw ValidationException::withMessages([
                'email' => 'Password reset disabled',
            ]);
        }
    }

    /**
     * Ensure the forgot password request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->securitySettings->max_reset_password_attempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
