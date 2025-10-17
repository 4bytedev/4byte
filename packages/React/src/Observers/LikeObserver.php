<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Like;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class LikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(Like $like)
    {
        $feedback = new Feedback('like', (string) $like->user_id, strtolower(class_basename($like->likeable)).':'.$like->likeable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(Like $like)
    {
        $this->gorse->deleteFeedback('like', (string) $like->user_id, strtolower(class_basename($like->likeable)).':'.$like->likeable->id);
    }
}
