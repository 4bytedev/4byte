<?php

namespace App\Http\Requests\Auth;

use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordRequest extends FormRequest
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
        return [
            'email'    => 'required|string|lowercase|email|max:255',
            'token'    => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#\$@!%&.*?])[A-Za-z\d#\$@!%&.*?]{8,}$/',
            ],
        ];
    }

    /**
     * Attempt to reset password the request's credentials.
     *
     * @throws ValidationException
     */
    public function resetPassword(): JsonResponse
    {
        $this->ensureResetPasswordEnabled();
        $this->ensureIsNotRateLimited();

        $data = $this->only(['email', 'token', 'password']);

        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $data,
            function (CanResetPassword|Model|Authenticatable $user) use ($data) {
                $user->forceFill([
                    'password'       => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => 'Try again later',
            ]);
        }

        return response()->json(['message' => 'Password successfully updated'], 200);
    }

    /**
     * Ensure the password reset request is allowed.
     *
     * @throws ValidationException
     */
    public function ensureResetPasswordEnabled(): void
    {
        if (! $this->securitySettings->password_reset_enabled) {
            throw ValidationException::withMessages([
                'email' => 'Password reset disabled',
            ]);
        }
    }

    /**
     * Ensure the reset password request is not rate limited.
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
