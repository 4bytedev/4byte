<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Models\Comment;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function isCommentedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever($this->getCacheKey().":{$userId}:commented", function () use ($userId) {
            return $this->comments()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    public function comment(int $userId, string $content, ?int $parentId = null): void
    {
        $this->comments()->create(['user_id' => $userId, 'parent_id' => $parentId, 'content' => $content]);
        if (isset($parentId)) {
            Cache::increment($this->getCacheKey().":comment:{$parentId}:replies");
        } else {
            Cache::increment($this->getCacheKey().':comments');
        }
        Cache::forever($this->getCacheKey().":{$userId}:commented", true);
    }

    public function commentsCount(): int
    {
        return Cache::rememberForever($this->getCacheKey().':comments', function () {
            return $this->comments()->count();
        });
    }

    public function commentRepliesCount(int $parentId): int
    {
        return Cache::rememberForever($this->getCacheKey().":comment:{$parentId}:replies", function () use ($parentId) {
            return $this->comments()->where('parent_id', $parentId)->count();
        });
    }
}
