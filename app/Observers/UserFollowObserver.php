<?php

namespace App\Observers;

use App\Models\UserFollow;
use Packages\Recommend\Services\GorseService;

class UserFollowObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(UserFollow $userFollow)
    {
        $gorseUser = $this->gorse->getUser($userFollow->follower_id);
        $gorseUser->labels[] = "user:{$userFollow->follower_id}";
        $this->gorse->updateUser($gorseUser);
    }

    public function deleted(UserFollow $userFollow)
    {
        $gorseUser = $this->gorse->getUser($userFollow->follower_id);
        $gorseUser->labels = array_filter($gorseUser->labels, fn ($label) => $label !== "user:{$userFollow->follower_id}");
        $this->gorse->updateUser($gorseUser);
    }
}
