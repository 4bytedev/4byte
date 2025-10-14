<?php

namespace Packages\Entry\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Packages\Entry\Services\EntryService;
use Packages\Recommend\Services\FeedService;

class EntryController extends Controller
{
    protected EntryService $entryService;

    protected SeoService $seoService;

    protected FeedService $feedService;

    public function __construct()
    {
        $this->entryService = app(EntryService::class);
        $this->seoService = app(SeoService::class);
        $this->feedService = app(FeedService::class);
    }

    public function view(Request $request)
    {
        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $entry = $this->entryService->getData($entryId);

        return Inertia::render('Entry/Detail', [
            'entry' => $entry,
        ])->withViewData(['seo' => $this->seoService->getEntrySEO($entry, $entry->user)]);
    }

    public function like(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $userId = Auth::id();
        $entryKey = "entry:{$entryId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$entryKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($entryId, $userId) {
                if (! $this->entryService->deleteLike($entryId, $userId)) {
                    $this->entryService->deleteDislike($entryId, $userId);
                    $this->entryService->insertLike($entryId, $userId);
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
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $userId = Auth::id();
        $entryKey = "entry:{$entryId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$entryKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($entryId, $userId) {
                if ($this->entryService->deleteDislike($entryId, $userId)) {
                    $this->entryService->deleteLike($entryId, $userId);
                    $this->entryService->insertDislike($entryId, $userId);
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
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $userId = Auth::id();
        if (! $this->entryService->deleteSave($entryId, $userId)) {
            $this->entryService->insertSave($entryId, $userId);
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
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $userId = Auth::id();
        $comment = $this->entryService->insertComment($entryId, $request->get('parent', null), $userId, $request->get('content'));

        return response()->json($comment);
    }

    public function commentList(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'slug' => 'required|string|exists:entry,slug',
        ]);
        $slug = $request->route('slug');
        $entryId = $this->entryService->getId($slug);

        $comments = $this->entryService->getComments($entryId, $request->integer('page', 1), 10);

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
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $commentId = $request->route('comment');
        $entryId = $this->entryService->getId($slug);

        $commentReplies = $this->entryService->getCommentReplies($entryId, $commentId, $request->integer('page', 1), 10);

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
            'slug' => 'required|string|exists:entry,slug',
        ]);

        $slug = $request->route('slug');
        $commentId = $request->route('comment');
        $entryId = $this->entryService->getId($slug);
        $userId = Auth::id();
        $entryKey = "entry:{$entryId}:comment:{$commentId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$entryKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($entryId, $commentId, $userId) {
                if (! $this->entryService->deleteCommentLike($entryId, $commentId, $userId)) {
                    $this->entryService->insertCommentLike($entryId, $commentId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}
