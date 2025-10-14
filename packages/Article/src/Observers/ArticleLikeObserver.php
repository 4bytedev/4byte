<?php

namespace Packages\Article\Observers;

use Carbon\Carbon;
use Packages\Article\Models\ArticleLike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class ArticleLikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(ArticleLike $articleLike)
    {
        $feedback = new Feedback('like', (string) $articleLike->user_id, 'article:'.$articleLike->article_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(ArticleLike $articleLike)
    {
        $this->gorse->deleteFeedback('like', (string) $articleLike->user_id, 'article:'.$articleLike->article_id);
    }
}
