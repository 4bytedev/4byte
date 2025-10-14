<?php

namespace Packages\Article\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Packages\Article\Services\ArticleService;
use Packages\Recommend\Services\FeedService;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    protected SeoService $seoService;

    protected FeedService $feedService;

    public function __construct()
    {
        $this->articleService = app(ArticleService::class);
        $this->seoService = app(SeoService::class);
        $this->feedService = app(FeedService::class);
    }

    public function view(Request $request)
    {
        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $article = $this->articleService->getData($articleId);

        return Inertia::render('Article/Detail', [
            'article' => $article,
        ])->withViewData(['seo' => $this->seoService->getArticleSEO($article, $article->user)]);
    }

    public function like(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $userId = Auth::id();
        $articleKey = "article:{$articleId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$articleKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($articleId, $userId) {
                if (! $this->articleService->deleteLike($articleId, $userId)) {
                    $this->articleService->deleteDislike($articleId, $userId);
                    $this->articleService->insertLike($articleId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }

    public function dislike(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $userId = Auth::id();
        $articleKey = "article:{$articleId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$articleKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($articleId, $userId) {
                if ($this->articleService->deleteDislike($articleId, $userId)) {
                    $this->articleService->deleteLike($articleId, $userId);
                    $this->articleService->insertDislike($articleId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }

    public function save(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $userId = Auth::id();
        if (! $this->articleService->deleteSave($articleId, $userId)) {
            $this->articleService->insertSave($articleId, $userId);
        }

        return response()->noContent(200);
    }

    public function comment(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'content' => 'required|string|min:20',
            'parent' => 'nullable|integer',
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $userId = Auth::id();
        $comment = $this->articleService->insertComment($articleId, $request->get('parent', null), $userId, $request->get('content'));

        return response()->json($comment);
    }

    public function commentList(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $articleId = $this->articleService->getId($slug);

        $comments = $this->articleService->getComments($articleId, $request->get('page', 1), 10);

        return response()->json($comments);
    }

    public function commentReplies(Request $request)
    {
        $request->merge([
            'comment' => $request->route('comment'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'comment' => 'required|integer|exists:comments,id',
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $commentId = $request->route('comment');
        $articleId = $this->articleService->getId($slug);

        $commentReplies = $this->articleService->getCommentReplies($articleId, $commentId, $request->integer('page', 1), 10);

        return response()->json($commentReplies);
    }

    public function commentLike(Request $request)
    {
        $request->merge([
            'comment' => $request->route('comment'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'comment' => 'required|integer|exists:comments,id',
            'slug' => 'required|string|exists:article,slug',
        ]);

        $slug = $request->route('slug');
        $commentId = $request->route('comment');
        $articleId = $this->articleService->getId($slug);
        $userId = Auth::id();
        $articleKey = "article:{$articleId}:comment:{$commentId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$articleKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($articleId, $commentId, $userId) {
                if (! $this->articleService->deleteCommentLike($articleId, $commentId, $userId)) {
                    $this->articleService->insertCommentLike($articleId, $commentId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}
