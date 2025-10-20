<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Save;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Services\GorseService;

class SaveObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "created" event for the Save model.
     */
    public function created(Save $save): void
    {
        /** @phpstan-ignore-next-line */
        $feedback = new GorseFeedback('save', (string) $save->user_id, strtolower(class_basename($save->saveable)) . ':' . $save->saveable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    /**
     * Handle the "deleted" event for the Save model.
     */
    public function deleted(Save $save): void
    {
        /* @phpstan-ignore-next-line */
        $this->gorse->deleteFeedback('save', (string) $save->user_id, strtolower(class_basename($save->saveable)) . ':' . $save->saveable->id);
    }
}
