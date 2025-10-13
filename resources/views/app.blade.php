<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ "/favicon.ico?v=2" }}">
        {!! isset($seo) ? $seo->generate() : null !!}
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', 'resources/css/app.css'])
        @inertiaHead

        <script>
            (function() {
                try {
                    let theme = localStorage.getItem('theme');
                    if (!theme || theme === 'system') {
                        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        theme = prefersDark ? 'dark' : 'light';
                    }
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (e) {
                    console.error('Theme init error:', e);
                }
            })();
        </script>
        <script async src="{{ "https://www.googletagmanager.com/gtag/js?id=" . App\Services\SettingsService::getSeoSettingsField('google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            (window.requestIdleCallback || function(cb){ setTimeout(cb, 2000); })(() => {
                gtag('config', '{{ App\Services\SettingsService::getSeoSettingsField('google_analytics_id') }}');
            });
        </script>
        {!! App\Services\SettingsService::getSeoSettingsField('extra_header') !!}
    </head>
    <body>
      @inertia
        {!! App\Services\SettingsService::getSeoSettingsField('extra_footer') !!}
    </body>
</html>
