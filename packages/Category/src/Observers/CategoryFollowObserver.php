<?php

namespace Packages\Category\Observers;

use Packages\Category\Models\CategoryFollow;
use Packages\Recommend\Services\GorseService;

class CategoryFollowObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(CategoryFollow $categoryFollow)
    {
        $gorseUser = $this->gorse->getUser((string) $categoryFollow->user_id);
        $gorseUser->labels[] = "category:{$categoryFollow->id}";
        $this->gorse->updateUser($gorseUser);
    }

    public function deleted(CategoryFollow $categoryFollow)
    {
        $gorseUser = $this->gorse->getUser((string) $categoryFollow->user_id);
        $gorseUser->labels = array_filter($gorseUser->labels, fn ($label) => $label !== "category:{$categoryFollow->category_id}");
        $this->gorse->updateUser($gorseUser);
    }
}
