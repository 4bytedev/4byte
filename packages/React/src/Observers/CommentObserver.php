<?php

namespace Packages\React\Observers;

use Carbon\Carbon;
use Packages\React\Models\Comment;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Services\GorseService;

class CommentObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(Comment $comment)
    {
        $feedback = new GorseFeedback('comment', (string) $comment->user_id, strtolower(class_basename($comment->commentable)).':'.$comment->commentable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(Comment $comment)
    {
        $this->gorse->deleteFeedback('comment', (string) $comment->user_id, strtolower(class_basename($comment->commentable)).':'.$comment->commentable->id);
    }
}
