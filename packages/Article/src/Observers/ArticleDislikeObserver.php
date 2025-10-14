<?php

namespace Packages\Article\Observers;

use Carbon\Carbon;
use Packages\Article\Models\ArticleDislike;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class ArticleDislikeObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(ArticleDislike $articleDislike)
    {
        $feedback = new Feedback('read', (string) $articleDislike->user_id, 'article:'.$articleDislike->article_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(ArticleDislike $articleDislike)
    {
        $this->gorse->deleteFeedback('read', (string) $articleDislike->user_id, 'article:'.$articleDislike->article_id);
    }
}
