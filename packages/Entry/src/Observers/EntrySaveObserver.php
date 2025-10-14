<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Packages\Entry\Models\EntrySave;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class EntrySaveObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(EntrySave $entrySave)
    {
        $feedback = new Feedback('save', (string) $entrySave->user_id, 'entry:'.$entrySave->entry_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(EntrySave $entrySave)
    {
        $this->gorse->deleteFeedback('save', (string) $entrySave->user_id, 'entry:'.$entrySave->entry_id);
    }
}
