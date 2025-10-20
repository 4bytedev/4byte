<?php

namespace Packages\Page\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Packages\Page\Models\Page;

class PageObserver
{
    /**
     * Handle the "updating" event for the Page model.
     */
    public function updating(Page $page): void
    {
        if ($page->isDirty('image')) {
            $oldPath = $page->getOriginal('image');

            if ($oldPath && Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }
    }

    /**
     * Handle the "updated" event for the Page model.
     */
    public function updated(Page $page): void
    {
        Cache::forget("page:{$page->id}");
    }

    /**
     * Handle the "deleted" event for the Page model.
     */
    public function deleted(Page $page): void
    {
        Cache::forget("page:{$page->slug}:id");
        Cache::forget("page:{$page->id}");
    }
}
