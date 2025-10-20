<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:' . User::class . ',email',
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:' . User::class . ',username',
                'regex:/^[a-z0-9]+$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#\$@!%&.*?])[A-Za-z\d#\$@!%&.*?]{8,}$/',
            ],
        ];
    }

    /**
     * Attempt to register the request's credentials.
     *
     * @throws ValidationException
     */
    public function register(): JsonResponse
    {
        $this->ensureRegisterEnabled();
        $this->ensureIsNotRateLimited();

        $data             = $this->only('name', 'email', 'username', 'password');
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $notification      = app(VerifyEmail::class);
        $notification->url = URL::temporarySignedRoute(
            'user.verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $user->notify($notification);
        Auth::login($user, true);

        event(new Registered($user));

        RateLimiter::clear($this->throttleKey());

        return response()->json([
            'name'     => $user->name,
            'username' => $user->username,
            'avatar'   => $user->getAvatarImage(),
            'verified' => false,
        ]);
    }

    /**
     * Ensure the email extension is allowed.
     *
     * @throws ValidationException
     */
    public function ensureEmailExtensionAllowed(): void
    {
        if (isset($this->securitySettings->allowed_emails) && $this->securitySettings->allowed_emails) {
            return;
        }
        $email   = $this->input('email');
        $allowed = false;
        foreach ($this->securitySettings->allowed_emails as $allowedEmail) {
            if (str_contains($email, $allowedEmail)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email extension'],
            ]);
        }
    }

    /**
     * Ensure the register request is allowed.
     *
     * @throws ValidationException
     */
    public function ensureRegisterEnabled(): void
    {
        if (! $this->securitySettings->register_enabled) {
            throw ValidationException::withMessages([
                'email' => 'Register disabled',
            ]);
        }
    }

    /**
     * Ensure the register request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->securitySettings->max_register_attempts)) {
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
        return Str::transliterate($this->ip());
    }
}
