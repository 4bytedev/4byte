<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Dislike;

trait HasDislikes
{
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }

    public function isDislikedBy(?int $userId): bool
    {
        if (!$userId) return false;

        return Cache::rememberForever($this->getCacheKey().":{$userId}:disliked", function () use ($userId) {
            return $this->dislikes()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    public function dislike(int $userId): void
    {
        if (! $this->isDislikedBy($userId)) {
            $this->dislikes()->create(['user_id' => $userId]);
            Cache::increment($this->getCacheKey().":dislikes");
            Cache::forever($this->getCacheKey().":{$userId}:disliked", true);
        }
    }

    public function undislike(int $userId): void
    {
        $deleted = $this->dislikes()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::decrement($this->getCacheKey().":dislikes");
            Cache::forget($this->getCacheKey().":{$userId}:disliked");
        }
    }

    public function toggleDislike(int $userId): void
    {
        $this->isDislikedBy($userId)
            ? $this->undislike($userId)
            : $this->dislike($userId);
    }

    public function dislikesCount(): int
    {
        return Cache::rememberForever($this->getCacheKey().":dislikes", function () {
            return $this->dislikes()->count();
        });
    }
}
