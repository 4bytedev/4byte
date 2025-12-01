<?php

namespace Packages\React\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Packages\React\Http\Requests\ReactRequest;
use Packages\React\Services\ReactService;

class ReactController extends Controller
{
    protected ReactService $reactService;

    public function __construct()
    {
        $this->reactService = app(ReactService::class);
    }

    /**
     * Handle like reaction on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function like(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId, $type] = $request->resolveTarget();
        $userId                      = Auth::id();

        $cacheKey = "{$type}:{$itemId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$cacheKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($baseClass, $itemId, $userId) {
                if (! $this->reactService->deleteLike($baseClass, $itemId, $userId)) {
                    $this->reactService->deleteDislike($baseClass, $itemId, $userId);
                    $this->reactService->insertLike($baseClass, $itemId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }

    /**
     * Handle dislike reaction on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function dislike(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId, $type] = $request->resolveTarget();
        $userId                      = Auth::id();

        $cacheKey = "{$type}:{$itemId}:reaction";

        $executed = RateLimiter::attempt(
            key: "{$cacheKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($baseClass, $itemId, $userId) {
                if (! $this->reactService->deleteDislike($baseClass, $itemId, $userId)) {
                    $this->reactService->deleteLike($baseClass, $itemId, $userId);
                    $this->reactService->insertDislike($baseClass, $itemId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }

    /**
     * Handle save action on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function save(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();

        if (! $this->reactService->deleteSave($baseClass, $itemId, $userId)) {
            $this->reactService->insertSave($baseClass, $itemId, $userId);
        }

        return response()->noContent();
    }

    /**
     * Create a new comment on a model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function comment(ReactRequest $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:20',
            'parent'  => 'nullable|integer',
        ]);

        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();
        $content              = $request->input('content');
        $parentId             = $request->input('parent', null);

        $comment = $this->reactService->insertComment($baseClass, $itemId, $content, $userId, $parentId);

        return response()->json($comment);
    }

    /**
     * Get paginated comments for a model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function comments(ReactRequest $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
        ]);

        [$baseClass, $itemId] = $request->resolveTarget();
        $page                 = $request->input('page', 1);

        $comments = $this->reactService->getComments($baseClass, $itemId, $page, 10);

        return response()->json($comments);
    }

    /**
     * Get replies for a specific comment.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function replies(ReactRequest $request): JsonResponse
    {
        $request->merge([
            'parent' => $request->route('parent'),
        ]);

        $request->validate([
            'page'   => 'sometimes|integer|min:1',
            'parent' => 'required|integer|exists:comments,id',
        ]);

        $parentId             = (int) $request->route('parent');
        [$baseClass, $itemId] = $request->resolveTarget();
        $page                 = $request->input('page', 1);

        if (! $parentId) {
            response()->noContent();
        }

        $commentReplies = $this->reactService->getCommentReplies($baseClass, $itemId, $parentId, $page, 10);

        return response()->json($commentReplies);
    }

    /**
     * Follow or unfollow a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function follow(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId, $type] = $request->resolveTarget();
        $userId                      = Auth::id();

        $cacheKey = "{$type}:{$itemId}:follow";

        $executed = RateLimiter::attempt(
            key: "{$cacheKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($baseClass, $itemId, $userId) {
                if (! $this->reactService->deleteFollow($baseClass, $itemId, $userId)) {
                    $this->reactService->insertFollow($baseClass, $itemId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}
