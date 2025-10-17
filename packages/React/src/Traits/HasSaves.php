<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Save;

trait HasSaves
{
    public function saves(): MorphMany
    {
        return $this->morphMany(Save::class, 'saveable');
    }

    public function isSavedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever($this->getCacheKey().":{$userId}:saved", function () use ($userId) {
            return $this->saves()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    public function saveFor(int $userId): void
    {
        if (! $this->isSavedBy($userId)) {
            $this->saves()->create(['user_id' => $userId]);
            Cache::forever($this->getCacheKey().":{$userId}:saved", true);
        }
    }

    public function unsave(int $userId): void
    {
        $deleted = $this->saves()->where('user_id', $userId)->delete();
        if ($deleted) {
            Cache::forget($this->getCacheKey().":{$userId}:saved");
        }
    }

    public function toggleSave(int $userId): void
    {
        $this->isSavedBy($userId)
            ? $this->unsave($userId)
            : $this->saveFor($userId);
    }
}
