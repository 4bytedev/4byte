<?php

namespace Packages\Tag\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Tag\Models\Tag;

class TagProfileObserver
{
    public function updated(Tag $tag)
    {
        Cache::forget("tag:{$tag->id}:profile");
    }

    public function deleted(Tag $tag)
    {
        Cache::forget("tag:{$tag->id}:profile");
    }
}
