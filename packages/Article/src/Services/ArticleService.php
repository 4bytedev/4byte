<?php

namespace Packages\Article\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Article\Data\ArticleCommentData;
use Packages\Article\Data\ArticleData;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleComment;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Article\Models\ArticleDislike;
use Packages\Article\Models\ArticleLike;
use Packages\Article\Models\ArticleSave;

class ArticleService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public function getData(int $articleId)
    {
        $article = Cache::rememberForever("article:{$articleId}", function () use ($articleId) {
            $article = Article::query()
                ->where('status', 'PUBLISHED')
                ->with(['categories:id,name,slug', 'tags:id,name,slug'])
                ->select(['id', 'title', 'slug', 'content', 'excerpt', 'sources', 'published_at', 'user_id'])
                ->findOrFail($articleId);

            return $article;
        });

        $user = $this->userService->getData($article->user_id);

        return ArticleData::fromModel($article, $user);
    }

    public function getId(string $slug)
    {
        return Cache::rememberForever("article:{$slug}:id", function () use ($slug) {
            return Article::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    public function checkLiked(int $articleId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("article:{$articleId}:{$userId}:liked");
    }

    public function checkDisliked(int $articleId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("article:{$articleId}:{$userId}:disliked");
    }

    public function checkSaved(int $articleId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("article:{$articleId}:{$userId}:saved");
    }

    public function getLikesCount(int $articleId)
    {
        return Cache::rememberForever("article:{$articleId}:likes", fn () => ArticleLike::where('article_id', $articleId)->count()
        );
    }

    public function getDislikesCount(int $articleId)
    {
        return Cache::rememberForever("article:{$articleId}:dislikes", fn () => ArticleDislike::where('article_id', $articleId)->count()
        );
    }

    public function insertLike(int $articleId, int $userId)
    {
        ArticleLike::create([
            'article_id' => $articleId,
            'user_id' => $userId,
        ]);
        Cache::increment("article:{$articleId}:likes");
        Cache::forever("article:{$articleId}:{$userId}:liked", true);
    }

    public function insertDislike(int $articleId, int $userId)
    {
        ArticleDislike::create([
            'article_id' => $articleId,
            'user_id' => $userId,
        ]);
        Cache::increment("article:{$articleId}:dislikes");
        Cache::forever("article:{$articleId}:{$userId}:disliked", true);
    }

    public function insertSave(int $articleId, int $userId)
    {
        ArticleSave::create([
            'article_id' => $articleId,
            'user_id' => $userId,
        ]);
        Cache::forever("article:{$articleId}:{$userId}:saved", true);
    }

    public function deleteLike(int $articleId, int $userId)
    {
        $deleted = ArticleLike::where('article_id', $articleId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::decrement("article:{$articleId}:likes");
            Cache::forget("article:{$articleId}:{$userId}:liked");
        }

        return $deleted;
    }

    public function deleteDislike(int $articleId, int $userId)
    {
        $deleted = ArticleDislike::where('article_id', $articleId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::decrement("article:{$articleId}:dislikes");
            Cache::forget("article:{$articleId}:{$userId}:disliked");
        }

        return $deleted;
    }

    public function deleteSave(int $articleId, int $userId)
    {
        $deleted = ArticleSave::where('article_id', $articleId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::forget("article:{$articleId}:{$userId}:saved");
        }

        return $deleted;
    }

    public function insertComment(int $articleId, ?int $parentId, int $userId, string $content)
    {
        if (! $parentId) {
            Cache::increment("article:{$articleId}:comments");
        } else {
            $parent = ArticleComment::where('parent_id', $parentId)
                ->where('article_id', $articleId)
                ->exists();
            if (! $parent) {
                throw new \RuntimeException('System Exception.', 500);
            }
            Cache::increment("article:{$articleId}:comment:{$parentId}");
        }
        $comment = ArticleComment::create([
            'content' => $content,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'article_id' => $articleId,
        ]);

        return ArticleCommentData::fromModel($comment);
    }

    public function getCommentsCount(int $articleId)
    {
        return Cache::rememberForever("article:{$articleId}:comments", fn () => ArticleComment::where('article_id', $articleId)->count()
        );
    }

    public function getComments(int $articleId, int $page, int $perPage)
    {
        $comments = ArticleComment::where('article_id', $articleId)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return ArticleCommentData::collect($comments);
    }

    public function getCommentRepliesCount(int $articleId, int $commentId)
    {
        return Cache::rememberForever("article:{$articleId}:comment:{$commentId}:replies", fn () => ArticleComment::where('article_id', $articleId)
            ->where('parent_id', $commentId)
            ->count()
        );
    }

    public function getCommentReplies(int $articleId, int $commentId, int $page, int $perPage)
    {
        $comments = ArticleComment::where('article_id', $articleId)
            ->where('parent_id', $commentId)
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return ArticleCommentData::collect($comments);
    }

    public function checkCommentLiked(int $articleId, int $commentId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::has("article:{$articleId}:comment:{$commentId}:{$userId}:liked");
    }

    public function getCommentLikesCount(int $articleId, int $commentId)
    {
        return Cache::rememberForever("article:{$articleId}:comment:{$commentId}:likes", fn () => ArticleCommentLike::where('article_id', $articleId)->where('comment_id', $commentId)->count()
        );
    }

    public function insertCommentLike(int $articleId, int $commentId, int $userId)
    {
        ArticleCommentLike::create([
            'article_id' => $articleId,
            'user_id' => $userId,
            'comment_id' => $commentId,
        ]);
        Cache::increment("article:{$articleId}:comment:{$commentId}:likes");
        Cache::forever("article:{$articleId}:comment:{$commentId}:{$userId}:liked", true);
    }

    public function deleteCommentLike(int $articleId, int $commentId, int $userId)
    {
        $deleted = ArticleCommentLike::where('article_id', $articleId)
            ->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->delete();
        if ($deleted) {
            Cache::decrement("article:{$articleId}:comment:{$commentId}:likes");
            Cache::forget("article:{$articleId}:comment:{$commentId}:{$userId}:liked");
        }

        return $deleted;
    }
}
