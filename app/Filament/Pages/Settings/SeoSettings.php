<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;

class SeoSettings
{
    public static function get(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('SEO Settings'))
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('seoSettings.meta_titleTemplate')
                            ->label(__('Title template')),
                        Forms\Components\TextInput::make('seoSettings.meta_description')
                            ->label(__('Meta description')),
                        Forms\Components\TextInput::make('seoSettings.meta_keywords')
                            ->label(__('Meta keywords (comma separated)')),
                    ])->columns(3),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('seoSettings.meta_canonicalEnabled')
                            ->label(__('Canonical enabled')),
                        Forms\Components\TextInput::make('seoSettings.meta_robots')
                            ->label(__('Robots meta tag')),
                        Forms\Components\KeyValue::make('seoSettings.meta_custom')
                            ->label('Custom Meta Tags'),
                    ])->columns(3),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('seoSettings.twitter_enabled')
                            ->label(__('Twitter enabled')),
                        Forms\Components\TextInput::make('seoSettings.twitter_site')
                            ->label(__('Twitter site (@username)')),
                        Forms\Components\TextInput::make('seoSettings.twitter_card')
                            ->label(__('Card type')),
                    ])->columns(3),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('seoSettings.twitter_title')
                            ->label(__('Twitter title')),
                        Forms\Components\TextInput::make('seoSettings.twitter_description')
                            ->label(__('Twitter description')),
                        Forms\Components\FileUpload::make('seoSettings.twitter_image')
                            ->image()
                            ->imageEditor()
                            ->storeFile(false)
                            ->label(__('Twitter image')),
                        Forms\Components\TextInput::make('seoSettings.twitter_imageAlt')
                            ->label(__('Twitter image alt text')),
                    ])->columns(2),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('seoSettings.og_enabled')
                            ->label(__('Open Graph enabled')),
                        Forms\Components\TextInput::make('seoSettings.og_site')
                            ->label(__('OG site name')),
                        Forms\Components\TextInput::make('seoSettings.og_type')
                            ->label(__('OG type')),
                        Forms\Components\TextInput::make('seoSettings.og_title')
                            ->label(__('OG title')),
                        Forms\Components\TextInput::make('seoSettings.og_description')
                            ->label(__('OG description')),
                        Forms\Components\FileUpload::make('seoSettings.og_images')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->label(__('OG images'))
                            ->storeFiles(false),
                        Forms\Components\TextInput::make('seoSettings.og_determiner')
                            ->label(__('OG determiner')),
                    ])->columns(3),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('seoSettings.jld_enabled')
                            ->label(__('JSON-LD enabled')),
                        Forms\Components\Toggle::make('seoSettings.jld_pretty')
                            ->label(__('Pretty print JSON-LD')),
                        Forms\Components\TextInput::make('seoSettings.jld_type')
                            ->label(__('JSON-LD type')),
                        Forms\Components\TextInput::make('seoSettings.jld_name')
                            ->label(__('JSON-LD name')),
                        Forms\Components\TextInput::make('seoSettings.jld_description')
                            ->label(__('JSON-LD description')),
                        Forms\Components\FileUpload::make('seoSettings.jld_images')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->label(__('JSON-LD images'))
                            ->storeFiles(false),
                        Forms\Components\KeyValue::make('seoSettings.jld_custom')
                            ->label(__('Custom JSON-LD fields')),
                        Forms\Components\Toggle::make('seoSettings.jld_placeOnGraph')
                            ->label(__('Place on graph automatically')),
                    ])->columns(3),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Toggle::make('seoSettings.sync_urlCanonical')
                            ->label(__('Sync canonical URL')),
                        Forms\Components\Toggle::make('seoSettings.sync_keywordsTags')
                            ->label(__('Sync keywords tags')),
                    ])->columns(2),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Textarea::make('seoSettings.extra_header')
                            ->label(__('Extra header code')),
                        Forms\Components\Textarea::make('seoSettings.extra_footer')
                            ->label(__('Extra footer code')),
                    ])->columns(2),
            ]);
    }
}
