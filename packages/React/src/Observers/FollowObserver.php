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

    /**
     * Handle the "created" event for the Follow model.
     */
    public function created(Follow $follow): void
    {
        $gorseUser = $this->gorse->getUser((string) $follow->follower_id);
        /* @phpstan-ignore-next-line */
        $gorseUser->addLabel(strtolower(class_basename($follow->followable)) . ':' . $follow->followable->id);
        $this->gorse->updateUser($gorseUser);

        FollowedEvent::dispatch($follow);
    }

    /**
     * Handle the "deleted" event for the Follow model.
     */
    public function deleted(Follow $follow): void
    {
        $gorseUser = $this->gorse->getUser((string) $follow->follower_id);
        /* @phpstan-ignore-next-line */
        $gorseUser->removeLabel(strtolower(class_basename($follow->followable)) . ':' . $follow->followable->id);
        $this->gorse->updateUser($gorseUser);
    }
}
