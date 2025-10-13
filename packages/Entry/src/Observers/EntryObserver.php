<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Packages\Entry\Models\Entry;
use Packages\Recommend\Services\GorseItem;
use Packages\Recommend\Services\GorseService;

class EntryObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function saved(Entry $entry)
    {
        $gorseItem = new GorseItem(
            'entry:'.$entry->id,
            ['entry', "user:{$entry->user_id}"],
            [],
            '',
            false,
            Carbon::now()->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    public function updated(Entry $entry)
    {
        Cache::forget("entry:{$entry->id}");
    }

    public function deleted(Entry $entry)
    {
        $this->gorse->deleteItem('entry:'.$entry->id);
        Cache::forget("entry:{$entry->slug}:id");
        Cache::forget("entry:{$entry->id}");
        Cache::forget("entry:{$entry->id}:likes");
        Cache::forget("entry:{$entry->id}:dislikes");
    }
}
