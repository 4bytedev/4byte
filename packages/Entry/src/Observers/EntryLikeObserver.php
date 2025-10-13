<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Packages\Entry\Models\EntryLike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class EntryLikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(EntryLike $entryLike)
    {
        $feedback = new Feedback('like', $entryLike->user_id, 'entry:'.$entryLike->entry_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(EntryLike $entryLike)
    {
        $this->gorse->deleteFeedback('like', $entryLike->user_id, 'entry:'.$entryLike->entry_id);
    }
}
