<?php

namespace Packages\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Packages\Category\Services\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->categoryService = app(CategoryService::class);
        $this->seoService = app(SeoService::class);
    }

    public function view(Request $request)
    {
        $slug = $request->route('slug');
        $categoryId = $this->categoryService->getId($slug);
        $category = $this->categoryService->getData($categoryId);
        $profile = $this->categoryService->getProfileData($categoryId);
        $articles = $this->categoryService->getArticlesCount($categoryId);
        $news = $this->categoryService->getNewsCount($categoryId);
        $followers = $this->categoryService->getFollowersCount($categoryId);
        $isFollowing = $this->categoryService->checkFollowing($categoryId, Auth::id());
        $tags = $this->categoryService->listTags($categoryId);

        return Inertia::render('Category/Detail', [
            'category' => $category,
            'profile' => $profile,
            'articles' => $articles,
            'news' => $news,
            'tags' => $tags,
            'followers' => $followers,
            'isFollowing' => $isFollowing,
        ])->withViewData(['seo' => $this->seoService->getCategorySeo($category, $profile)]);
    }

    public function follow(Request $request)
    {
        $slug = $request->route('slug');
        $currentUser = Auth::user();

        $executed = RateLimiter::attempt(
            key: "{$currentUser->username}:tag:{$slug}:follow",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($currentUser, $slug) {
                $categoryId = $this->categoryService->getId($slug);
                if (! $this->categoryService->deleteFollow($categoryId, $currentUser->id)) {
                    $this->categoryService->insertFollow($categoryId, $currentUser->id);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}
