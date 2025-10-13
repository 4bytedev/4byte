<?php

namespace Packages\Entry\Observers;

use Carbon\Carbon;
use Packages\Entry\Models\EntryCommentLike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class EntryCommentLikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(EntryCommentLike $entryCommentLike)
    {
        $feedback = new Feedback('like', $entryCommentLike->user_id, "entry:{$entryCommentLike->entry_id}:comment:{$entryCommentLike->comment_id}", '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(EntryCommentLike $entryCommentLike)
    {
        $this->gorse->deleteFeedback('like', $entryCommentLike->user_id, "entry:{$entryCommentLike->entry_id}:comment:{$entryCommentLike->comment_id}");
    }
}
