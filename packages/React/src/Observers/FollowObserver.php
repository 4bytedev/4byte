<?php

namespace Packages\React\Observers;

use Packages\React\Events\FollowedEvent;
use Packages\React\Models\Follow;
use Packages\Recommend\Services\GorseService;

class FollowObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(Follow $follow)
    {
        $gorseUser = $this->gorse->getUser((string) $follow->follower_id);
        $gorseUser->labels[] = strtolower(class_basename($follow->followable)).':'.$follow->followable->id;
        $this->gorse->updateUser($gorseUser);

        FollowedEvent::dispatch($follow);
    }

    public function deleted(Follow $follow)
    {
        $gorseUser = $this->gorse->getUser((string) $follow->follower_id);
        $gorseUser->labels = array_filter($gorseUser->labels, fn ($label) => $label !== strtolower(class_basename($follow->followable)).':'.$follow->followable->id);
        $this->gorse->updateUser($gorseUser);
    }
}
