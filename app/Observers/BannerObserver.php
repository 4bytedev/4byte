<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\ArticleSave;

class BannerObserver
{
    public function saved(ArticleSave $articleSave)
    {
        Cache::forget('banners');
    }

    public function deleted(ArticleSave $articleSave)
    {
        Cache::forget('banners');
    }
}
