<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;

class SecuritySettings
{
    public static function get(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('Security Settings'))
            ->icon('heroicon-o-lock-closed')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Fieldset::make(__('Max login attempts'))
                            ->schema([
                                Forms\Components\TextInput::make('securitySettings.max_login_attempts')
                                    ->label(__('Max login attempts'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('securitySettings.max_login_attempts_seconds')
                                    ->label(__('Max login attempts in seconds'))
                                    ->numeric()
                                    ->required(),
                            ])->columnSpan(1),
                        Forms\Components\Fieldset::make(__('Max register attempts'))
                            ->schema([
                                Forms\Components\TextInput::make('securitySettings.max_register_attempts')
                                    ->label(__('Max register attempts'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('securitySettings.max_register_attempts_seconds')
                                    ->label(__('Max register attempts in seconds'))
                                    ->numeric()
                                    ->required(),
                            ])->columnSpan(1),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Fieldset::make(__('Max reset password attempts'))
                            ->schema([
                                Forms\Components\TextInput::make('securitySettings.max_reset_password_attempts')
                                    ->label(__('Max reset password attempts'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('securitySettings.max_reset_password_attempts_seconds')
                                    ->label(__('Max reset password attempts in seconds'))
                                    ->numeric()
                                    ->required(),
                            ])->columnSpan(1),
                        Forms\Components\Fieldset::make(__('Max email verification attempts'))
                            ->schema([
                                Forms\Components\TextInput::make('securitySettings.max_email_verification_attempts')
                                    ->label(__('Max email verification attempts'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('securitySettings.max_email_verification_attempts_seconds')
                                    ->label(__('Max email verification attempts in seconds'))
                                    ->numeric()
                                    ->required(),
                            ])->columnSpan(1),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('securitySettings.login_enabled')
                            ->label(__('Login enabled')),
                        Forms\Components\Toggle::make('securitySettings.register_enabled')
                            ->label(__('Register enabled')),
                        Forms\Components\Toggle::make('securitySettings.password_reset_enabled')
                            ->label(__('Password reset enabled')),
                    ])->columns(3),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('securitySettings.two_factor_authentication_enabled')
                            ->label(__('Two factor authentication enabled')),
                        Forms\Components\Toggle::make('securitySettings.remember_me_enabled')
                            ->label(__('Remember me enabled')),
                        Forms\Components\Toggle::make('securitySettings.captcha_login_enabled')
                            ->label(__('Login captcha enabled')),
                        Forms\Components\Toggle::make('securitySettings.captcha_register_enabled')
                            ->label(__('Register captcha enabled')),
                        Forms\Components\Toggle::make('securitySettings.captcha_reset_password_enabled')
                            ->label(__('Reset password captcha enabled')),
                        Forms\Components\Toggle::make('securitySettings.captcha_email_verification_enabled')
                            ->label(__('Email verification captcha enabled')),
                    ])->columns(3),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('securitySettings.force_ssl')
                            ->label(__('Force SSL')),
                        Forms\Components\Toggle::make('securitySettings.under_maintenance')
                            ->label(__('Under maintenance')),
                        Forms\Components\Toggle::make('securitySettings.email_verification_required')
                            ->label(__('Email verification required')),
                    ])->columns(3),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Repeater::make('securitySettings.allowed_emails')
                            ->label(__('Allowed emails'))
                            ->schema([
                                Forms\Components\TextInput::make('allowed_email')->required(),
                            ])
                            ->addable()
                            ->reorderable(false)
                            ->deletable(),
                    ])->columns(3),
            ]);
    }
}
