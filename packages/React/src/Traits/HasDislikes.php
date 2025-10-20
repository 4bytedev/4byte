<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Dislike;

trait HasDislikes
{
    /**
     * @return MorphMany<Dislike, $this>
     */
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }

    /**
     * Determine whether the given user has disliked this model.
     */
    public function isDislikedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever(Helpers::cacheKey($this, $userId, 'disliked'), function () use ($userId) {
            return $this->dislikes()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    /**
     * Add a dislike from the given user.
     */
    public function dislike(int $userId): void
    {
        if (! $this->isDislikedBy($userId)) {
            $this->dislikes()->create(['user_id' => $userId]);
            Cache::increment(Helpers::cacheKey($this, 'dislikes'));
            Cache::forever(Helpers::cacheKey($this, $userId, 'disliked'), true);
        }
    }

    /**
     * Remove a dislike by the given user.
     */
    public function undislike(int $userId): void
    {
        $deleted = $this->dislikes()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::decrement(Helpers::cacheKey($this, 'dislikes'));
            Cache::forget(Helpers::cacheKey($this, $userId, 'disliked'));
        }
    }

    /**
     * Toggle the dislike state for the given user.
     */
    public function toggleDislike(int $userId): void
    {
        $this->isDislikedBy($userId)
            ? $this->undislike($userId)
            : $this->dislike($userId);
    }

    /**
     * Retrieve the total number of dislikes for this model.
     */
    public function dislikesCount(): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'dislikes'), function () {
            return $this->dislikes()->count();
        });
    }
}
