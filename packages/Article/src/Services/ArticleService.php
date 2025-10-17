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

    public function getData(int $articleId)
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

    public function getId(string $slug)
    {
        return Cache::rememberForever("article:{$slug}:id", function () use ($slug) {
            return Article::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
