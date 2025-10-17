<?php

namespace Packages\React\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Packages\React\Data\CommentData;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Follow;
use Packages\React\Models\Like;
use Packages\React\Models\Save;

class ReactService
{
    public function insertLike(string $likeableType, int $likeableId, int $userId): void
    {
        Like::create([
            'user_id' => $userId,
            'likeable_id' => $likeableId,
            'likeable_type' => $likeableType,
        ]);

        Cache::increment($this->cacheKey($likeableType, $likeableId, 'likes'));
        Cache::forever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), true);
    }

    public function deleteLike(string $likeableType, int $likeableId, int $userId): bool
    {
        $deleted = Like::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->delete();

        if ($deleted) {
            Cache::decrement($this->cacheKey($likeableType, $likeableId, 'likes'));
            Cache::forget($this->cacheKey($likeableType, $likeableId, $userId, 'liked'));
        }

        return (bool) $deleted;
    }

    public function getLikesCount(string $likeableType, int $likeableId): int
    {
        return Cache::rememberForever(
            $this->cacheKey($likeableType, $likeableId, 'likes'),
            fn () => Like::where('likeable_id', $likeableId)
                ->where('likeable_type', $likeableType)
                ->count()
        );
    }

    public function checkLiked(string $likeableType, int $likeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), function () use ($likeableType, $likeableId, $userId) {
            return Like::where([
                'user_id' => $userId,
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableType,
            ])->exists();
        });
    }

    public function insertDislike(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        Dislike::create([
            'user_id' => $userId,
            'dislikeable_id' => $dislikeableId,
            'dislikeable_type' => $dislikeableType,
        ]);

        Cache::increment($this->cacheKey($dislikeableType, $dislikeableId, 'dislikes'));
        Cache::forever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), true);
    }

    public function deleteDislike(string $dislikeableType, int $dislikeableId, int $userId): bool
    {
        $deleted = Dislike::where('user_id', $userId)
            ->where('dislikeable_id', $dislikeableId)
            ->where('dislikeable_type', $dislikeableType)
            ->delete();

        if ($deleted) {
            Cache::decrement($this->cacheKey($dislikeableType, $dislikeableId, 'dislikes'));
            Cache::forget($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'));
        }

        return (bool) $deleted;
    }

    public function getDislikesCount(string $dislikeableType, int $dislikeableId): int
    {
        return Cache::rememberForever(
            $this->cacheKey($dislikeableType, $dislikeableId, 'dislikes'),
            fn () => Dislike::where('dislikeable_id', $dislikeableId)
                ->where('dislikeable_type', $dislikeableType)
                ->count()
        );
    }

    public function checkDisliked(string $dislikeableType, int $dislikeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), function () use ($dislikeableType, $dislikeableId, $userId) {
            return Dislike::where([
                'user_id' => $userId,
                'dislikeable_id' => $dislikeableId,
                'dislikeable_type' => $dislikeableType,
            ])->exists();
        });
    }

    public function insertSave(string $saveableType, int $saveableId, int $userId)
    {
        Save::create([
            'user_id' => $userId,
            'saveable_id' => $saveableId,
            'saveable_type' => $saveableType,
        ]);
        Cache::forever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), true);
    }

    public function deleteSave(string $saveableType, int $saveableId, int $userId)
    {
        $deleted = Save::where('user_id', $userId)
            ->where('saveable_id', $saveableId)
            ->where('saveable_type', $saveableType)
            ->delete();
        if ($deleted) {
            Cache::forget($this->cacheKey($saveableType, $saveableId, $userId, 'saved'));
        }

        return $deleted;
    }

    public function checkSaved(string $saveableType, int $saveableId, int $userId)
    {
        return Cache::rememberForever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), function () use ($saveableType, $saveableId, $userId) {
            return Save::where([
                'user_id' => $userId,
                'saveable_id' => $saveableId,
                'saveable_type' => $saveableType,
            ])->exists();
        });
    }

    public function insertComment(string $commentableType, int $commentableId, string $content, int $userId, ?int $parentId = null)
    {
        if (! $parentId) {
            Cache::increment($this->cacheKey($commentableType, $commentableId, 'comments'));
        } else {
            Comment::where('id', $parentId)
                ->where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->existsOrFail();
            Cache::increment($this->cacheKey($commentableType, $commentableId, 'comments'));
            Cache::increment($this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies'));
            Cache::forever($this->cacheKey($commentableType, $commentableId, $userId, 'commented'), true);
        }
        $comment = Comment::create([
            'content' => $content,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'commentable_id' => $commentableId,
            'commentable_type' => $commentableType,
        ]);

        return CommentData::fromModel($comment);
    }

    public function getCommentsCount(string $commentableType, int $commentableId)
    {
        return Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comments'),
            fn () => Comment::where('commentable_id', $commentableId)->where('commentable_type', $commentableType)->count()
        );
    }

    public function getComment(int $commentId)
    {
        $comment = Cache::rememberForever($this->cacheKey(Comment::class, $commentId, 'comment', $commentId), function () use ($commentId) {
            return Comment::where('id', $commentId)->first();
        });

        return CommentData::fromModel($comment, false, false, false, true, true, true);
    }

    public function getComments(string $commentableType, int $commentableId, int $page, int $perPage)
    {
        $comments = Comment::where('commentable_id', $commentableId)
            ->where('commentable_type', $commentableType)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return CommentData::collect($comments);
    }

    public function getCommentRepliesCount(string $commentableType, int $commentableId, ?int $parentId)
    {
        if (! $parentId) {
            return 0;
        }

        return Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies'),
            fn () => Comment::where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->where('parent_id', $parentId)
                ->count()
        );
    }

    public function getCommentReplies(string $commentableType, int $commentableId, int $parentId, int $page, int $perPage)
    {
        $comments = Comment::where('commentable_id', $commentableId)
            ->where('commentable_type', $commentableType)
            ->where('parent_id', $parentId)
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return CommentData::collect($comments);
    }

    public function insertFollow(string $followableType, int $followableId, int $followerId): void
    {
        Follow::create([
            'follower_id' => $followerId,
            'followable_id' => $followableId,
            'followable_type' => $followableType,
        ]);

        Cache::increment($this->cacheKey($followableType, $followableId, 'followers'));
        Cache::forever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), true);
    }

    public function deleteFollow(string $followableType, int $followableId, int $followerId): bool
    {
        $deleted = Follow::where('follower_id', $followerId)
            ->where('followable_id', $followableId)
            ->where('followable_type', $followableType)
            ->delete();

        if ($deleted) {
            Cache::decrement($this->cacheKey($followableType, $followableId, 'followers'));
            Cache::forget($this->cacheKey($followableType, $followableId, $followerId, 'followed'));
        }

        return (bool) $deleted;
    }

    public function getFollowersCount(string $followableType, int $followableId): int
    {
        return Cache::rememberForever($this->cacheKey($followableType, $followableId, 'followers'), function () use ($followableId, $followableType) {
            return Follow::where('followable_id', $followableId)
                ->where('followable_type', $followableType)
                ->count();
        });
    }

    public function getFollowingsCount(int $userId): int
    {
        return Cache::rememberForever($this->cacheKey(User::class, $userId, 'followings'), function () use ($userId) {
            return Follow::where('follower_id', $userId)
                ->count();
        });
    }

    public function checkFollowed(string $followableType, int $followableId, int $followerId): bool
    {
        return Cache::rememberForever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), function () use ($followerId, $followableId, $followableType) {
            return Follow::where([
                'follower_id' => $followerId,
                'followable_id' => $followableId,
                'followable_type' => $followableType,
            ])->exists();
        });
    }

    protected function cacheKey(string $reactableType, int $reactableId, ...$parts): string
    {
        $base = strtolower(class_basename($reactableType));

        return "{$base}:{$reactableId}:".implode(':', $parts);
    }
}
