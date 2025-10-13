<?php

namespace Packages\Tag\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Tag\Models\Tag;

class TagObserver
{
    public function updated(Tag $tag)
    {
        Cache::forget("tag:{$tag->id}");
    }

    public function deleted(Tag $tag)
    {
        Cache::forget("tag:{$tag->slug}:id");
        Cache::forget("tag:{$tag->id}");
        Cache::forget("tag:{$tag->id}:articles");
        Cache::forget("tag:{$tag->id}:news");
        Cache::forget("tag:{$tag->id}:followers");
    }
}
