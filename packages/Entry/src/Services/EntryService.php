<?php

namespace Packages\Entry\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Entry\Data\EntryCommentData;
use Packages\Entry\Data\EntryData;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryComment;
use Packages\Entry\Models\EntryCommentLike;
use Packages\Entry\Models\EntryDislike;
use Packages\Entry\Models\EntryLike;
use Packages\Entry\Models\EntrySave;

class EntryService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public function getData(int $entryId)
    {
        $entry = Cache::rememberForever("entry:{$entryId}", function () use ($entryId) {
            $entry = Entry::select(['id', 'slug', 'content', 'user_id', 'created_at'])
                ->findOrFail($entryId);

            return $entry;
        });

        $user = $this->userService->getData($entry->user_id);

        return EntryData::fromModel($entry, $user);
    }

    public function getId(string $slug)
    {
        return Cache::rememberForever("entry:{$slug}:id", function () use ($slug) {
            return Entry::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    public function checkLiked(int $entryId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("entry:{$entryId}:{$userId}:liked");
    }

    public function checkDisliked(int $entryId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("entry:{$entryId}:{$userId}:disliked");
    }

    public function checkSaved(int $entryId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("entry:{$entryId}:{$userId}:saved");
    }

    public function getLikesCount(int $entryId)
    {
        return Cache::rememberForever("entry:{$entryId}:likes", fn () => EntryLike::where('entry_id', $entryId)->count()
        );
    }

    public function getDislikesCount(int $entryId)
    {
        return Cache::rememberForever("entry:{$entryId}:dislikes", fn () => EntryDislike::where('entry_id', $entryId)->count()
        );
    }

    public function insertLike(int $entryId, int $userId)
    {
        EntryLike::create([
            'entry_id' => $entryId,
            'user_id' => $userId,
        ]);
        Cache::increment("entry:{$entryId}:likes");
        Cache::forever("entry:{$entryId}:{$userId}:liked", true);
    }

    public function insertDislike(int $entryId, int $userId)
    {
        EntryDislike::create([
            'entry_id' => $entryId,
            'user_id' => $userId,
        ]);
        Cache::increment("entry:{$entryId}:dislikes");
        Cache::forever("entry:{$entryId}:{$userId}:disliked", true);
    }

    public function insertSave(int $entryId, int $userId)
    {
        EntrySave::create([
            'entry_id' => $entryId,
            'user_id' => $userId,
        ]);
        Cache::forever("entry:{$entryId}:{$userId}:saved", true);
    }

    public function deleteLike(int $entryId, int $userId)
    {
        $deleted = EntryLike::where('entry_id', $entryId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::decrement("entry:{$entryId}:likes");
            Cache::forget("entry:{$entryId}:{$userId}:liked");
        }

        return $deleted;
    }

    public function deleteDislike(int $entryId, int $userId)
    {
        $deleted = EntryDislike::where('entry_id', $entryId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::decrement("entry:{$entryId}:dislikes");
            Cache::forget("entry:{$entryId}:{$userId}:disliked");
        }

        return $deleted;
    }

    public function deleteSave(int $entryId, int $userId)
    {
        $deleted = EntrySave::where('entry_id', $entryId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::forget("entry:{$entryId}:{$userId}:saved");
        }

        return $deleted;
    }

    public function insertComment(int $entryId, ?int $parentId, int $userId, string $content)
    {
        if (! $parentId) {
            Cache::increment("entry:{$entryId}:comments");
        } else {
            $parent = EntryComment::where('parent_id', $parentId)
                ->where('entry_id', $entryId)
                ->exists();
            if (! $parent) {
                throw new \RuntimeException('System Exception.', 500);
            }
            Cache::increment("entry:{$entryId}:comment:{$parentId}");
        }
        $comment = EntryComment::create([
            'content' => $content,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'entry_id' => $entryId,
        ]);

        return EntryCommentData::fromModel($comment);
    }

    public function getCommentsCount(int $entryId)
    {
        return Cache::rememberForever("entry:{$entryId}:comments", fn () => EntryComment::where('entry_id', $entryId)->count()
        );
    }

    public function getComments(int $entryId, int $page, int $perPage)
    {
        $comments = EntryComment::where('entry_id', $entryId)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return EntryCommentData::collect($comments);
    }

    public function getCommentRepliesCount(int $entryId, int $commentId)
    {
        return Cache::rememberForever("entry:{$entryId}:comment:{$commentId}:replies", fn () => EntryComment::where('entry_id', $entryId)
            ->where('parent_id', $commentId)
            ->count()
        );
    }

    public function getCommentReplies(int $entryId, int $commentId, int $page, int $perPage)
    {
        $comments = EntryComment::where('entry_id', $entryId)
            ->where('parent_id', $commentId)
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return EntryCommentData::collect($comments);
    }

    public function checkCommentLiked(int $entryId, int $commentId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("entry:{$entryId}:comment:{$commentId}:{$userId}:liked");
    }

    public function getCommentLikesCount(int $entryId, int $commentId)
    {
        return Cache::rememberForever("entry:{$entryId}:comment:{$commentId}:likes", fn () => EntryCommentLike::where('entry_id', $entryId)->where('comment_id', $commentId)->count()
        );
    }

    public function insertCommentLike(int $entryId, int $commentId, int $userId)
    {
        EntryCommentLike::create([
            'entry_id' => $entryId,
            'user_id' => $userId,
            'comment_id' => $commentId,
        ]);
        Cache::increment("entry:{$entryId}:comment:{$commentId}:likes");
        Cache::forever("entry:{$entryId}:comment:{$commentId}:{$userId}:liked", true);
    }

    public function deleteCommentLike(int $entryId, int $commentId, int $userId)
    {
        $deleted = EntryCommentLike::where('entry_id', $entryId)
            ->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->delete();
        if ($deleted) {
            Cache::decrement("entry:{$entryId}:comment:{$commentId}:likes");
            Cache::forget("entry:{$entryId}:comment:{$commentId}:{$userId}:liked");
        }

        return $deleted;
    }
}
