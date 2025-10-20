<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Follow;

trait CanFollow
{
    /**
     * @return HasMany<Follow, $this>
     */
    public function followings(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    /**
     * Get the total number of followings for this model.
     */
    public function followingsCount(): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'followings'), function () {
            return $this->followings()->count();
        });
    }

    /**
     * Follow a given target model.
     *
     * @param object $target
     */
    public function follow($target): void
    {
        if (! $this->isFollowing($target)) {
            $this->followings()->create([
                'followable_id'   => $target->id,
                'followable_type' => $target::class,
            ]);

            Cache::increment(Helpers::cacheKey($this, 'followers'));
            Cache::increment(Helpers::cacheKey($this, 'followings'));
            Cache::forever(Helpers::cacheKey($this, $this->id, 'followed'), true);
        }
    }

    /**
     * Unfollow a given target model.
     *
     * @param object $target
     */
    public function unfollow($target): void
    {
        $this->followings()
            ->where('followable_id', $target->id)
            ->where('followable_type', $target::class)
            ->delete();

        Cache::decrement(Helpers::cacheKey($this, 'followers'));
        Cache::decrement(Helpers::cacheKey($this, 'followings'));
        Cache::forget(Helpers::cacheKey($this, $this->id, 'followed'));
    }

    /**
     * Determine if this model is following the given target.
     *
     * @param object $target
     */
    public function isFollowing($target): bool
    {
        return Cache::rememberForever(
            Helpers::cacheKey($this, $this->id, 'followed'),
            function () use ($target) {
                return $this->followings()
                    ->where('followable_id', $target->id)
                    ->where('followable_type', $target::class)
                    ->exists();
            }
        );
    }
}
