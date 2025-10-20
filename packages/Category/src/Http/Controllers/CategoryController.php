<?php

namespace Packages\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Category\Services\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->categoryService = app(CategoryService::class);
        $this->seoService      = app(SeoService::class);
    }

    /**
     * Display a category detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug       = $request->route('slug');
        $categoryId = $this->categoryService->getId($slug);
        $category   = $this->categoryService->getData($categoryId);
        $profile    = $this->categoryService->getProfileData($categoryId);
        $articles   = $this->categoryService->getArticlesCount($categoryId);
        $news       = $this->categoryService->getNewsCount($categoryId);
        $tags       = $this->categoryService->listTags($categoryId);

        return Inertia::render('Category/Detail', [
            'category' => $category,
            'profile'  => $profile,
            'articles' => $articles,
            'news'     => $news,
            'tags'     => $tags,
        ])->withViewData(['seo' => $this->seoService->getCategorySeo($category, $profile)]);
    }
}
