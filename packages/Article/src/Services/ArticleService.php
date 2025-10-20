<?php

namespace Packages\Article\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Article\Data\ArticleData;
use Packages\Article\Models\Article;

class ArticleService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    /**
     * Retrieve article data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $articleId): ArticleData
    {
        $article = Cache::rememberForever("article:{$articleId}", function () use ($articleId) {
            return Article::query()
                ->where('status', 'PUBLISHED')
                ->with(['categories:id,name,slug', 'tags:id,name,slug'])
                ->select(['id', 'title', 'slug', 'content', 'excerpt', 'sources', 'status', 'published_at', 'user_id'])
                ->findOrFail($articleId);
        });

        $user = $this->userService->getData($article->user_id);

        return ArticleData::fromModel($article, $user);
    }

    /**
     * Retrieve the ID of a article by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("article:{$slug}:id", function () use ($slug) {
            return Article::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
