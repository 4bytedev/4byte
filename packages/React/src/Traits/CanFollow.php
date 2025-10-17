<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Follow;

trait CanFollow
{
    public function followings(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followingsCount(): int
    {
        return Cache::rememberForever($this->getCacheKey().":followings", function () {
            return $this->followings()->count();
        });
    }

    public function follow($target): void
    {
        if (! $this->isFollowing($target)) {
            $this->followings()->create([
                'followable_id' => $target->id,
                'followable_type' => get_class($target),
            ]);

            Cache::increment($target->getCacheKey().":followers");
            Cache::increment($this->getCacheKey().":followings");
            Cache::forever($target->getCacheKey().":{$this->id}:followed", true);
        }
    }

    public function unfollow($target): void
    {
        $this->followings()
            ->where('followable_id', $target->id)
            ->where('followable_type', get_class($target))
            ->delete();

        Cache::decrement($target->getCacheKey().":followers");
        Cache::decrement($this->getCacheKey().":followings");
        Cache::forget($target->getCacheKey().":{$this->id}:followed");
    }

    public function isFollowing($target): bool
    {
        return Cache::rememberForever(
            $this->cacheKey().":{$this->id}:followed",
            function () use ($target) {
                return $this->followings()
                    ->where('followable_id', $target->id)
                    ->where('followable_type', get_class($target))
                    ->exists();
            }
        );
    }
}
