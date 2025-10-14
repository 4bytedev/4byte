<?php

namespace Packages\Article\Observers;

use Carbon\Carbon;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class ArticleCommentLikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(ArticleCommentLike $articleCommentLike)
    {
        $feedback = new Feedback('like', (string) $articleCommentLike->user_id, "article:{$articleCommentLike->article_id}:comment:{$articleCommentLike->comment_id}", '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(ArticleCommentLike $articleCommentLike)
    {
        $this->gorse->deleteFeedback('like', (string) $articleCommentLike->user_id, "article:{$articleCommentLike->article_id}:comment:{$articleCommentLike->comment_id}");
    }
}
