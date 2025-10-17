<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class BannerObserver
{
    public function saved()
    {
        Cache::forget('banners');
    }

    public function deleted()
    {
        Cache::forget('banners');
    }
}
