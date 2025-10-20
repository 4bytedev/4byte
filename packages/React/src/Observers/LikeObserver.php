<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Like;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Services\GorseService;

class LikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "created" event for the Like model.
     */
    public function created(Like $like): void
    {
        /** @phpstan-ignore-next-line */
        $feedback = new GorseFeedback('like', (string) $like->user_id, strtolower(class_basename($like->likeable)) . ':' . $like->likeable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    /**
     * Handle the "deleted" event for the Like model.
     */
    public function deleted(Like $like): void
    {
        /* @phpstan-ignore-next-line */
        $this->gorse->deleteFeedback('like', (string) $like->user_id, strtolower(class_basename($like->likeable)) . ':' . $like->likeable->id);
    }
}
