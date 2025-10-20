<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Follow;

trait HasFollowers
{
    /**
     * @return MorphMany<Follow, $this>
     */
    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    /**
     * Retrieve the total number of followers for this model.
     */
    public function followersCount(): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'followers'), function () {
            return $this->followers()->count();
        });
    }

    /**
     * Determine if the specified user is following this model.
     */
    public function isFollowedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever(Helpers::cacheKey($this, $userId, 'followed'), function () use ($userId) {
            return $this->followers()->where('follower_id', $userId)->exists();
        });
    }
}
