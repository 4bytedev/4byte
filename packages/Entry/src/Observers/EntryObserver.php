<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Packages\Entry\Models\Entry;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Services\GorseService;

class EntryObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "saved" event for the Entry model.
     */
    public function saved(Entry $entry): void
    {
        $gorseItem = new GorseItem(
            'entry:' . $entry->id,
            ['entry', "user:{$entry->user_id}"],
            [],
            '',
            false,
            Carbon::now()->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    /**
     * Handle the "updated" event for the Entry model.
     */
    public function updated(Entry $entry): void
    {
        Cache::forget("entry:{$entry->id}");
    }

    /**
     * Handle the "deleted" event for the Entry model.
     */
    public function deleted(Entry $entry): void
    {
        $this->gorse->deleteItem("entry:{$entry->id}");
        Cache::forget("entry:{$entry->slug}:id");
        Cache::forget("entry:{$entry->id}");
        Cache::forget("entry:{$entry->id}:likes");
        Cache::forget("entry:{$entry->id}:dislikes");
    }
}
