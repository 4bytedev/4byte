<?php

namespace Packages\Article\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Article\Http\Requests\CreateRequest;
use Packages\Article\Http\Requests\EditRequest;
use Packages\Article\Models\Article;
use Packages\Category\Models\Category;
use Packages\Recommend\Services\FeedService;
use Packages\Tag\Models\Tag;

class ArticleCrudController extends Controller
{
    protected SeoService $seoService;

    protected FeedService $feedService;

    public function __construct()
    {
        $this->seoService     = app(SeoService::class);
        $this->feedService    = app(FeedService::class);
    }

    /**
     * Display a article create page.
     */
    public function createView(): Response
    {
        $topCategories = $this->feedService->categories();

        $topTags = $this->feedService->tags();

        return Inertia::render('Article/Create', [
            'topCategories' => $topCategories,
            'topTags'       => $topTags,
        ])->withViewData(['seo' => $this->seoService->getArticleCreateSEO()]);
    }

    /**
     * Creates a new Article.
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $isDraft = ! $request->boolean('published', false);

        $data = $request->validated();

        $image = null;

        if ($isDraft) {
            $extra = $request->only(['excerpt', 'content', 'image', 'categories', 'tags', 'sources']);
            $data  = array_merge($data, $extra);
        }

        $slug = $request->createSlug();

        $article = Article::create([
            'title'        => $data['title'],
            'slug'         => $slug,
            'excerpt'      => $data['excerpt'] ?? null,
            'content'      => $data['content'] ?? null,
            'status'       => $isDraft ? 'DRAFT' : 'PUBLISHED',
            'published_at' => $isDraft ? null : now(),
            'image'        => $image,
            'sources'      => $data['sources'] ?? [],
            'user_id'      => Auth::id(),
        ]);

        if ($request->hasFile('image')) {
            $article->addMediaFromRequest('image')->toMediaCollection('article');
        }

        if (isset($data['categories'])) {
            $categoryIds = Category::whereIn('slug', $data['categories'])->pluck('id')->toArray();
            $article->categories()->sync($categoryIds);
        }

        if (isset($data['tags'])) {
            $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
            $article->tags()->sync($tagIds);
        }

        return response()->json(['slug' => $slug]);
    }

    /**
     * Display a article edit page.
     */
    public function editView(Article $article): Response
    {
        $topCategories = $this->feedService->categories();

        $topTags = $this->feedService->tags();

        return Inertia::render('Article/Edit', [
            'topCategories' => $topCategories,
            'topTags'       => $topTags,
            'slug'          => $article->slug,
            'article'       => [
                'title'      => $article->title,
                'excerpt'    => $article->excerpt,
                'content'    => $article->content,
                'categories' => $article->categories->pluck('slug'),
                'tags'       => $article->tags->pluck('slug'),
                'published'  => $article->status === 'PUBLISHED',
                'image'      => $article->getCoverImage()['image'],
            ],
        ])->withViewData(['seo' => $this->seoService->getArticleEditSEO()]);
    }

    /**
     * Edits a existing Article.
     */
    public function edit(EditRequest $request, Article $article): JsonResponse
    {
        $isDraft = ! $request->boolean('published', false);

        $data = $request->validated();

        $image = null;

        if ($isDraft) {
            $extra = $request->only(['excerpt', 'content', 'image', 'categories', 'tags', 'sources']);
            $data  = array_merge($data, $extra);
        }

        if (isset($data['image'])) {
            $path  = $data['image']->store('article/images');
            $image = $path;
        }

        $slug = $request->createSlug($article->id);

        $article->update([
            'title'        => $data['title'],
            'slug'         => $slug,
            'excerpt'      => $data['excerpt'] ?? null,
            'content'      => $data['content'] ?? null,
            'status'       => $isDraft ? 'DRAFT' : 'PUBLISHED',
            'published_at' => $isDraft ? null : now(),
            'image'        => $image,
            'sources'      => $data['sources'] ?? [],
            'user_id'      => Auth::id(),
        ]);

        if (isset($data['categories'])) {
            $categoryIds = Category::whereIn('slug', $data['categories'])->pluck('id')->toArray();
            $article->categories()->sync($categoryIds);
        }

        if (isset($data['tags'])) {
            $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
            $article->tags()->sync($tagIds);
        }

        return response()->json(['slug' => $slug]);
    }
}
