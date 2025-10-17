<?php

namespace Packages\Tag\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Packages\Category\Services\CategoryService;
use Packages\Tag\Services\TagService;

class TagController extends Controller
{
    protected TagService $tagService;

    protected CategoryService $categoryService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->tagService = app(TagService::class);
        $this->categoryService = app(CategoryService::class);
        $this->seoService = app(SeoService::class);
    }

    public function view(Request $request)
    {
        $slug = $request->route('slug');
        $tagId = $this->tagService->getId($slug);
        $tag = $this->tagService->getData($tagId);
        $profile = $this->tagService->getProfileData($tagId);
        $articles = $this->tagService->getArticlesCount($tagId);
        $news = $this->tagService->getNewsCount($tagId);
        $categoryId = $this->categoryService->getId($profile->category->slug);
        $tags = $this->categoryService->listTags($categoryId);

        return Inertia::render('Tag/Detail', [
            'tag' => $tag,
            'profile' => $profile,
            'articles' => $articles,
            'news' => $news,
            'tags' => $tags,
        ])->withViewData(['seo' => $this->seoService->getTagSeo($tag, $profile)]);
    }
}
