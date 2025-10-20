<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Like;

trait HasLikes
{
    /**
     * @return MorphMany<Like, $this>
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Determine whether the given user has liked this model.
     */
    public function isLikedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever(Helpers::cacheKey($this, $userId, 'liked'), function () use ($userId) {
            return $this->likes()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    /**
     * Add a like from the given user.
     */
    public function like(int $userId): void
    {
        if (! $this->isLikedBy($userId)) {
            $this->likes()->create(['user_id' => $userId]);
            Cache::increment(Helpers::cacheKey($this, 'likes'));
            Cache::forever(Helpers::cacheKey($this, $userId, 'liked'), true);
        }
    }

    /**
     * Remove a like by the given user.
     */
    public function unlike(int $userId): void
    {
        $deleted = $this->likes()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::decrement(Helpers::cacheKey($this, 'likes'));
            Cache::forget(Helpers::cacheKey($this, $userId, 'liked'));
        }
    }

    /**
     * Toggle the like state for the given user.
     */
    public function toggleLike(int $userId): void
    {
        $this->isLikedBy($userId)
            ? $this->unlike($userId)
            : $this->like($userId);
    }

    /**
     * Retrieve the total number of likes for this model.
     */
    public function likesCount(): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'likes'), function () {
            return $this->likes()->count();
        });
    }
}
