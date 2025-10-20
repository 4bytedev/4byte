<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SecuritySettings extends Settings
{
    public ?int $max_login_attempts = 5;

    public ?int $max_login_attempts_seconds = 60;

    public ?int $max_register_attempts = 2;

    public ?int $max_register_attempts_seconds = 60;

    public ?int $max_reset_password_attempts = 2;

    public ?int $max_reset_password_attempts_seconds = 60;

    public ?int $max_email_verification_attempts = 2;

    public ?int $max_email_verification_attempts_seconds = 60;

    public ?bool $login_enabled = true;

    public ?bool $register_enabled = true;

    public ?bool $password_reset_enabled = true;

    public ?bool $two_factor_authentication_enabled = true;

    public ?bool $captcha_login_enabled = false;

    public ?bool $captcha_register_enabled = false;

    public ?bool $captcha_reset_password_enabled = false;

    public ?bool $captcha_email_verification_enabled = false;

    public ?bool $remember_me_enabled = true;

    public ?bool $force_ssl = true;

    public ?bool $under_maintenance = false;

    public ?bool $email_verification_required = true;

    /** @var array<int, string>|null */
    public ?array $allowed_emails = ['@gmail.com', '@hotmail.com'];

    public static function group(): string
    {
        return 'SecuritySettings';
    }
}
