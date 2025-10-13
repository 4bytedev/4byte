<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Packages\Entry\Models\EntryDislike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class EntryDislikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(EntryDislike $entryDislike)
    {
        $feedback = new Feedback('read', $entryDislike->user_id, 'entry:'.$entryDislike->entry_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(EntryDislike $entryDislike)
    {
        $this->gorse->deleteFeedback('read', $entryDislike->user_id, 'entry:'.$entryDislike->entry_id);
    }
}
