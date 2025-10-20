<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Save;

trait HasSaves
{
    /**
     * @return MorphMany<Save, $this>
     */
    public function saves(): MorphMany
    {
        return $this->morphMany(Save::class, 'saveable');
    }

    /**
     * Determine whether the given user has saved this model.
     */
    public function isSavedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever(Helpers::cacheKey($this, $userId, 'saved'), function () use ($userId) {
            return $this->saves()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    /**
     * Add a save from the given user.
     */
    public function saveFor(int $userId): void
    {
        if (! $this->isSavedBy($userId)) {
            $this->saves()->create(['user_id' => $userId]);
            Cache::forever(Helpers::cacheKey($this, $userId, 'saved'), true);
        }
    }

    /**
     * Remove a save by the given user.
     */
    public function unsave(int $userId): void
    {
        $deleted = $this->saves()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::forget(Helpers::cacheKey($this, $userId, 'saved'));
        }
    }

    /**
     * Toggle the save state for the given user.
     */
    public function toggleSave(int $userId): void
    {
        $this->isSavedBy($userId)
            ? $this->unsave($userId)
            : $this->saveFor($userId);
    }
}
