<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Dislike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class DislikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(Dislike $dislike)
    {
        $feedback = new Feedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)).':'.$dislike->dislikeable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(Dislike $dislike)
    {
        $this->gorse->deleteFeedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)).':'.$dislike->dislikeable->id);
    }
}
