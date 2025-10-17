<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Like;

trait HasLikes
{
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) return false;

        return Cache::rememberForever($this->getCacheKey().":{$userId}:liked", function () use ($userId) {
            return $this->likes()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    public function like(int $userId): void
    {
        if (! $this->isLikedBy($userId)) {
            $this->likes()->create(['user_id' => $userId]);
            Cache::increment($this->getCacheKey().":likes");
            Cache::forever($this->getCacheKey().":{$userId}:liked", true);
        }
    }

    public function unlike(int $userId): void
    {
        $deleted = $this->likes()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::decrement($this->getCacheKey().":likes");
            Cache::forget($this->getCacheKey().":{$userId}:liked");
        }
    }

    public function toggleLike(int $userId): void
    {
        $this->isLikedBy($userId)
            ? $this->unlike($userId)
            : $this->like($userId);
    }

    public function likesCount(): int
    {
        return Cache::rememberForever($this->getCacheKey().":likes", function () {
            return $this->likes()->count();
        });
    }
}
