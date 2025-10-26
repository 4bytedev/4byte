<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Packages\Page\Models\Page;
use Spatie\Permission\Models\Role as SpatieRole;

class SiteSettings
{
    public static function get(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('Site Settings'))
            ->icon('heroicon-o-cog')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('siteSettings.title')
                            ->label(__('Title'))
                            ->required(),
                        Forms\Components\TextInput::make('siteSettings.panel_url')
                            ->label(__('Panel URI'))
                            ->required(),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\FileUpload::make('siteSettings.light_logo')
                            ->image()
                            ->imageEditor()
                            ->label(__('Light logo'))
                            ->imagePreviewHeight('150')
                            ->storeFiles(false),
                        Forms\Components\FileUpload::make('siteSettings.dark_logo')
                            ->image()
                            ->imageEditor()
                            ->directory('user/images')
                            ->label(__('Dark logo'))
                            ->imagePreviewHeight('150')
                            ->storeFiles(false),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\FileUpload::make('siteSettings.favicon')
                            ->image()
                            ->imageEditor()
                            ->label(__('Favicon'))
                            ->directory('site/images')
                            ->imagePreviewHeight('150')
                            ->storeFiles(false),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Repeater::make('siteSettings.available_languages')
                            ->label(__('Available languages'))
                            ->schema([
                                Forms\Components\TextInput::make('available_language')->required(),
                            ])
                            ->addable()
                            ->deletable()
                            ->required(),
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('siteSettings.default_language')
                                ->label(__('Default language'))
                                ->options(function (callable $get) {
                                    /** @var array<int, string> $languages */
                                    $languages = $get('siteSettings.available_languages') ?? [];

                                    return collect($languages)
                                        ->pluck('available_language')
                                        ->filter(fn ($lang) => ! is_null($lang) && $lang !== '')
                                        ->mapWithKeys(fn ($lang) => [$lang => $lang])
                                        ->toArray();
                                })
                                ->reactive()
                                ->disabled(false)
                                ->required(),

                            Forms\Components\Select::make('siteSettings.default_role')
                                ->label(__('Default Role'))
                                ->options(SpatieRole::pluck('name', 'id')->toArray())
                                ->required(),
                        ]),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('siteSettings.terms_and_conditions_url')
                            ->label(__('Terms and Conditions Page'))
                            ->options(
                                Page::pluck('slug', 'slug')->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('siteSettings.privacy_policy_url')
                            ->label(__('Privacy policy URI'))
                            ->options(
                                Page::pluck('slug', 'slug')->toArray()
                            )
                            ->searchable()
                            ->required(),
                    ]),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('siteSettings.theme_mode')
                            ->label(__('Theme mode'))
                            ->options(['light' => 'Light', 'dark' => 'Dark', 'system' => 'System'])
                            ->required(),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('siteSettings.dark_mode_enabled')
                                    ->label(__('Dark mode enabled'))
                                    ->required(),
                                Forms\Components\Toggle::make('siteSettings.spa_enabled')
                                    ->label(__('Spa enabled')),
                            ])->columnSpan(1),
                    ])->columns(2),
            ]);
    }
}
