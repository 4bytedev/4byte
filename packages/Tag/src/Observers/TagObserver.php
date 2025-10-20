<?php

namespace Packages\Tag\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Tag\Models\Tag;

class TagObserver
{
    /**
     * Handle the "updated" event for the Tag model.
     */
    public function updated(Tag $tag): void
    {
        Cache::forget("tag:{$tag->id}");
    }

    /**
     * Handle the "deleted" event for the Tag model.
     */
    public function deleted(Tag $tag): void
    {
        Cache::forget("tag:{$tag->slug}:id");
        Cache::forget("tag:{$tag->id}");
        Cache::forget("tag:{$tag->id}:articles");
        Cache::forget("tag:{$tag->id}:news");
        Cache::forget("tag:{$tag->id}:followers");
    }
}
