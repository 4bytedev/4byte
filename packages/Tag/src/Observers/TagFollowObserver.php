<?php

namespace Packages\Tag\Observers;

use Packages\Recommend\Services\GorseService;
use Packages\Tag\Models\TagFollow;

class TagFollowObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(TagFollow $tagFollow)
    {
        $gorseUser = $this->gorse->getUser((string) $tagFollow->user_id);
        $gorseUser->labels[] = "tag:{$tagFollow->id}";
        $this->gorse->updateUser($gorseUser);
    }

    public function deleted(TagFollow $tagFollow)
    {
        $gorseUser = $this->gorse->getUser((string) $tagFollow->user_id);
        $gorseUser->labels = array_filter($gorseUser->labels, fn ($label) => $label !== "tag:{$tagFollow->category_id}");
        $this->gorse->updateUser($gorseUser);
    }
}
