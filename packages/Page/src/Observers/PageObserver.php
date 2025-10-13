<?php

namespace Packages\Page\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Packages\Page\Models\Page;

class PageObserver
{
    public function updating(Page $page)
    {
        if ($page->isDirty('image')) {
            $oldPath = $page->getOriginal('image');

            if ($oldPath && Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }
    }

    public function updated(Page $page): void
    {
        Cache::forget("page:{$page->id}");
    }

    public function deleted(Page $page): void
    {
        Cache::forget("page:{$page->slug}:id");
        Cache::forget("page:{$page->id}");
    }
}
