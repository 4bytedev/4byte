<?php

namespace Packages\React\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Packages\React\Data\CommentData;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Follow;
use Packages\React\Models\Like;
use Packages\React\Models\Save;

class ReactService
{
    /**
     * Inserts a like for the given user on the specified model.
     */
    public function insertLike(string $likeableType, int $likeableId, int $userId): void
    {
        Like::create([
            'user_id'       => $userId,
            'likeable_id'   => $likeableId,
            'likeable_type' => $likeableType,
        ]);

        Cache::increment($this->cacheKey($likeableType, $likeableId, 'likes'));
        Cache::forever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), true);
    }

    /**
     * Deletes a like from the given user on the specified model.
     */
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

    /**
     * Returns the total number of likes for the given model.
     */
    public function getLikesCount(string $likeableType, int $likeableId): int
    {
        return Cache::rememberForever(
            $this->cacheKey($likeableType, $likeableId, 'likes'),
            fn () => Like::where('likeable_id', $likeableId)
                ->where('likeable_type', $likeableType)
                ->count()
        );
    }

    /**
     * Checks if the given user has liked the specified model.
     */
    public function checkLiked(string $likeableType, int $likeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), function () use ($likeableType, $likeableId, $userId) {
            return Like::where([
                'user_id'       => $userId,
                'likeable_id'   => $likeableId,
                'likeable_type' => $likeableType,
            ])->exists();
        });
    }

    /**
     * Inserts a dislike for the given user on the specified model.
     */
    public function insertDislike(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        Dislike::create([
            'user_id'          => $userId,
            'dislikeable_id'   => $dislikeableId,
            'dislikeable_type' => $dislikeableType,
        ]);

        Cache::increment($this->cacheKey($dislikeableType, $dislikeableId, 'dislikes'));
        Cache::forever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), true);
    }

    /**
     * Deletes a dislike from the given user on the specified model.
     */
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

    /**
     * Returns the total number of dislikes for the given model.
     */
    public function getDislikesCount(string $dislikeableType, int $dislikeableId): int
    {
        return Cache::rememberForever(
            $this->cacheKey($dislikeableType, $dislikeableId, 'dislikes'),
            fn () => Dislike::where('dislikeable_id', $dislikeableId)
                ->where('dislikeable_type', $dislikeableType)
                ->count()
        );
    }

    /**
     * Checks if the given user has disliked the specified model.
     */
    public function checkDisliked(string $dislikeableType, int $dislikeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), function () use ($dislikeableType, $dislikeableId, $userId) {
            return Dislike::where([
                'user_id'          => $userId,
                'dislikeable_id'   => $dislikeableId,
                'dislikeable_type' => $dislikeableType,
            ])->exists();
        });
    }

    /**
     * Inserts a save for the given user on the specified model.
     */
    public function insertSave(string $saveableType, int $saveableId, int $userId): void
    {
        Save::create([
            'user_id'       => $userId,
            'saveable_id'   => $saveableId,
            'saveable_type' => $saveableType,
        ]);
        Cache::forever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), true);
    }

    /**
     * Deletes a save for the given user on the specified model.
     */
    public function deleteSave(string $saveableType, int $saveableId, int $userId): bool
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

    /**
     * Checks if the given user has saved the specified model.
     */
    public function checkSaved(string $saveableType, int $saveableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), function () use ($saveableType, $saveableId, $userId) {
            return Save::where([
                'user_id'       => $userId,
                'saveable_id'   => $saveableId,
                'saveable_type' => $saveableType,
            ])->exists();
        });
    }

    /**
     * Inserts a comment for the given user on the specified model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function insertComment(string $commentableType, int $commentableId, string $content, int $userId, ?int $parentId = null): CommentData
    {
        if ($parentId) {
            Comment::where('id', $parentId)
                ->where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->existsOrFail();
            Cache::increment($this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies'));
            try {
                $replyPaginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies', 'pagination') . '*');

                if (!empty($replyPaginationKeys)) {
                    Redis::del(...$replyPaginationKeys);
                }
            } catch (\Exception $e) {
                logger()->warning('Redis is not avaliable: ' . $e->getMessage());
            }
        }

        Cache::increment($this->cacheKey($commentableType, $commentableId, 'comments'));
        Cache::forever($this->cacheKey($commentableType, $commentableId, $userId, 'commented'), true);

        try {
            $paginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comments', 'pagination') . '*');

            if (!empty($paginationKeys)) {
                Redis::del(...$paginationKeys);
            }
        } catch (\Exception $e) {
            logger()->warning('Redis is not avaliable: ' . $e->getMessage());
        }

        $comment = Comment::create([
            'content'          => $content,
            'user_id'          => $userId,
            'parent_id'        => $parentId,
            'commentable_id'   => $commentableId,
            'commentable_type' => $commentableType,
        ]);

        return CommentData::fromModel($comment);
    }

    /**
     * Returns the total number of comments for the given model.
     */
    public function getCommentsCount(string $commentableType, int $commentableId): int
    {
        return Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comments'),
            function () use ($commentableType, $commentableId) {
                return Comment::where('commentable_id', $commentableId)->where('commentable_type', $commentableType)->count();
            }
        );
    }

    /**
     * Returns a single comment by its ID.
     */
    public function getComment(int $commentId): CommentData
    {
        $comment = Cache::rememberForever($this->cacheKey(Comment::class, $commentId, 'comment', $commentId), function () use ($commentId) {
            return Comment::where('id', $commentId)->first();
        });

        return CommentData::fromModel($comment, false, false, false, true, true, true);
    }

    /**
     * Returns a paginated collection of top-level comments for the given model.
     *
     * @return Collection<int, CommentData>
     */
    public function getComments(string $commentableType, int $commentableId, int $page, int $perPage): Collection
    {
        $comments = Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comments', 'pagination', (string) $page, (string) $perPage),
            fn () => Comment::where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->whereNull('parent_id')
                ->orderByDesc('created_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
        );

        return CommentData::collect($comments);
    }

    /**
     * Returns the number of replies for a specific comment.
     */
    public function getCommentRepliesCount(string $commentableType, int $commentableId, ?int $parentId): int
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

    /**
     * Returns a paginated collection of replies for a specific comment.
     *
     * @return Collection<int, CommentData>
     */
    public function getCommentReplies(string $commentableType, int $commentableId, int $parentId, int $page, int $perPage): Collection
    {
        $comments = Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies', 'pagination', $page, $perPage),
            fn() => Comment::where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->where('parent_id', $parentId)
                ->orderByDesc('created_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
        );

        return CommentData::collect($comments);
    }

    /**
     * Inserts a follow relationship for the given user.
     */
    public function insertFollow(string $followableType, int $followableId, int $followerId): void
    {
        Follow::create([
            'follower_id'     => $followerId,
            'followable_id'   => $followableId,
            'followable_type' => $followableType,
        ]);

        Cache::increment($this->cacheKey($followableType, $followableId, 'followers'));
        Cache::forever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), true);
    }

    /**
     * Deletes a follow relationship for the given user.
     */
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

    /**
     * Returns the number of followers for a specific model.
     */
    public function getFollowersCount(string $followableType, int $followableId): int
    {
        return Cache::rememberForever($this->cacheKey($followableType, $followableId, 'followers'), function () use ($followableId, $followableType) {
            return Follow::where('followable_id', $followableId)
                ->where('followable_type', $followableType)
                ->count();
        });
    }

    /**
     * Returns the number of users the given user is following.
     */
    public function getFollowingsCount(int $userId): int
    {
        return Cache::rememberForever($this->cacheKey(User::class, $userId, 'followings'), function () use ($userId) {
            return Follow::where('follower_id', $userId)
                ->count();
        });
    }

    /**
     * Checks if the given user is following the specified model.
     */
    public function checkFollowed(string $followableType, int $followableId, int $followerId): bool
    {
        return Cache::rememberForever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), function () use ($followerId, $followableId, $followableType) {
            return Follow::where([
                'follower_id'     => $followerId,
                'followable_id'   => $followableId,
                'followable_type' => $followableType,
            ])->exists();
        });
    }

    /**
     * Generates a cache key for reactable entities.
     *
     * @param string|int ...$parts
     */
    protected function cacheKey(string $reactableType, int $reactableId, ...$parts): string
    {
        $base = strtolower(class_basename($reactableType));

        return "{$base}:{$reactableId}:" . implode(':', $parts);
    }
}
