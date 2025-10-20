<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('SecuritySettings.max_login_attempts');
        $this->migrator->add('SecuritySettings.max_login_attempts_seconds');
        $this->migrator->add('SecuritySettings.max_register_attempts');
        $this->migrator->add('SecuritySettings.max_register_attempts_seconds');
        $this->migrator->add('SecuritySettings.max_reset_password_attempts');
        $this->migrator->add('SecuritySettings.max_reset_password_attempts_seconds');
        $this->migrator->add('SecuritySettings.max_email_verification_attempts');
        $this->migrator->add('SecuritySettings.max_email_verification_attempts_seconds');
        $this->migrator->add('SecuritySettings.force_ssl');
        $this->migrator->add('SecuritySettings.login_enabled');
        $this->migrator->add('SecuritySettings.register_enabled');
        $this->migrator->add('SecuritySettings.password_reset_enabled');
        $this->migrator->add('SecuritySettings.under_maintenance');
        $this->migrator->add('SecuritySettings.session_timeout');
        $this->migrator->add('SecuritySettings.two_factor_authentication_enabled');
        $this->migrator->add('SecuritySettings.captcha_login_enabled');
        $this->migrator->add('SecuritySettings.captcha_register_enabled');
        $this->migrator->add('SecuritySettings.captcha_reset_password_enabled');
        $this->migrator->add('SecuritySettings.captcha_email_verification_enabled');
        $this->migrator->add('SecuritySettings.remember_me_enabled');
        $this->migrator->add('SecuritySettings.email_verification_required');
        $this->migrator->add('SecuritySettings.allowed_emails');
    }
};
