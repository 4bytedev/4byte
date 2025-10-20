<?php

namespace Packages\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Packages\React\Helpers;
use Packages\React\Models\Comment;

trait HasComments
{
    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Determine if the specified user has commented on this model.
     */
    public function isCommentedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever(Helpers::cacheKey($this, $userId, 'commented'), function () use ($userId) {
            return $this->comments()
                ->where('user_id', $userId)
                ->exists();
        });
    }

    /**
     * Add a new comment by a specific user.
     */
    public function comment(int $userId, string $content, ?int $parentId = null): void
    {
        $this->comments()->create(['user_id' => $userId, 'parent_id' => $parentId, 'content' => $content]);
        if (isset($parentId)) {
            Cache::increment(Helpers::cacheKey($this, 'comment', $parentId, 'replies'));
        } else {
            Cache::increment(Helpers::cacheKey($this, 'comments'));
        }
        Cache::forever(Helpers::cacheKey($this, $userId, 'commented'), true);
    }

    /**
     * Retrieve the total number of comments for this model.
     */
    public function commentsCount(): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'comments'), function () {
            return $this->comments()->count();
        });
    }

    /**
     * Retrieve the number of replies for a specific parent comment.
     */
    public function commentRepliesCount(int $parentId): int
    {
        return Cache::rememberForever(Helpers::cacheKey($this, 'comment', $parentId, 'replies'), function () use ($parentId) {
            return $this->comments()->where('parent_id', $parentId)->count();
        });
    }
}
