<?php

namespace Packages\Article\Observers;

use Carbon\Carbon;
use Packages\Article\Models\ArticleSave;
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseService;

class ArticleSaveObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(ArticleSave $articleSave)
    {
        $feedback = new Feedback('save', (string) $articleSave->user_id, 'article:'.$articleSave->article_id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    public function deleted(ArticleSave $articleSave)
    {
        $this->gorse->deleteFeedback('save', (string) $articleSave->user_id, 'article:'.$articleSave->article_id);
    }
}
