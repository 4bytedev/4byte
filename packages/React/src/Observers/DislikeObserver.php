<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Dislike;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Services\GorseService;

class DislikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "created" event for the Dislike model.
     */
    public function created(Dislike $dislike): void
    {
        /** @phpstan-ignore-next-line */
        $feedback = new GorseFeedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)) . ':' . $dislike->dislikeable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    /**
     * Handle the "deleted" event for the Dislike model.
     */
    public function deleted(Dislike $dislike): void
    {
        /* @phpstan-ignore-next-line */
        $this->gorse->deleteFeedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)) . ':' . $dislike->dislikeable->id);
    }
}
