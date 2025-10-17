<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Follow;

trait HasFollowers
{
    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function followersCount(): int
    {
        return Cache::rememberForever($this->getCacheKey().':followers', function () {
            return $this->followers()->count();
        });
    }

    public function isFollowedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever($this->getCacheKey().":{$userId}:followed", function () use ($userId) {
            return $this->followers()->where('follower_id', $userId)->exists();
        });
    }
}
