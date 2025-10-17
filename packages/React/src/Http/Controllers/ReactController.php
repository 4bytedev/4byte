<?php

namespace Packages\React\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Packages\React\Services\ReactService;

class ReactController extends Controller
{
    protected ReactService $reactService;

    public function __construct()
    {
        $this->reactService = app(ReactService::class);
    }

    public function like(Request $request)
    {
        $request->merge([
            'type' => $request->route('type'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $userId = Auth::id();

        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $itemId = 0;

        if ($serviceClass === 'self') {
            $itemId = $request->slug;
        } else {
            $service = app($serviceClass);
            $itemId = $service->getId($request->slug);
        }

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

    public function dislike(Request $request)
    {
        $request->merge([
            'type' => $request->route('type'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $userId = Auth::id();

        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $service = app($serviceClass);

        $itemId = $service->getId($request->slug);
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

    public function save(Request $request)
    {
        $request->merge([
            'type' => $request->route('type'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $userId = Auth::id();

        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $service = app($serviceClass);

        $itemId = $service->getId($request->slug);

        if (! $this->reactService->deleteSave($baseClass, $itemId, $userId)) {
            $this->reactService->insertSave($baseClass, $itemId, $userId);
        }

        return response()->noContent(200);
    }

    public function comment(Request $request)
    {
        $request->merge([
            'type' => $request->route('type'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'content' => 'required|string|min:20',
            'parent' => 'nullable|integer',
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $userId = Auth::id();

        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $service = app($serviceClass);

        $itemId = $service->getId($request->slug);
        $comment = $this->reactService->insertComment($baseClass, $itemId, $request->get('content'), $userId, $request->get('parent', null));

        return response()->json($comment);
    }

    public function comments(Request $request)
    {
        $request->merge([
            'slug' => $request->route('slug'),
            'type' => $request->route('type'),
        ]);

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $service = app($serviceClass);

        $itemId = $service->getId($request->slug);

        $comments = $this->reactService->getComments($baseClass, $itemId, $request->get('page', 1), 10);

        return response()->json($comments);
    }

    public function replies(Request $request)
    {
        $request->merge([
            'parent' => $request->route('parent'),
            'slug' => $request->route('slug'),
            'type' => $request->route('type'),
        ]);

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'parent' => 'required|integer|exists:comments,id',
            'slug' => 'required|string',
            'type' => 'required|string',
        ]);

        $type = $request->get('type');
        $parentId = $request->get('parent');

        $serviceClass = config('react.callbacks')[$type];
        $baseClass = config('react.classes')[$type];

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $service = app($serviceClass);

        $itemId = $service->getId($request->slug);

        $commentReplies = $this->reactService->getCommentReplies($baseClass, $itemId, $parentId, $request->integer('page', 1), 10);

        return response()->json($commentReplies);
    }

    public function follow(Request $request)
    {
        $request->merge([
            'type' => $request->route('type'),
            'slug' => $request->route('slug'),
        ]);

        $request->validate([
            'type' => 'required|string',
            'slug' => 'required|string',
        ]);

        $userId = Auth::id();
        $type = $request->get('type');

        $serviceClass = config('react.callbacks')[$type] ?? null;
        $baseClass = config('react.classes')[$type] ?? null;

        if (! isset($serviceClass) || ! isset($baseClass)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if ($serviceClass === 'self') {
            $itemId = $request->slug;
        } else {
            $service = app($serviceClass);
            $itemId = $service->getId($request->slug);
        }

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
