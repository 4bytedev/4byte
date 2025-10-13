<?php

namespace Packages\News\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\News\Data\NewsData;
use Packages\News\Models\News;

class NewsService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public function getData(int $newsId)
    {
        return Cache::rememberForever("news:{$newsId}", function () use ($newsId) {
            $news = News::where('status', 'PUBLISHED')
                ->with(['categories:id,name,slug', 'tags:id,name,slug'])
                ->select(['title', 'slug', 'content', 'excerpt', 'image', 'published_at', 'user_id'])
                ->findOrFail($newsId);

            return NewsData::fromModel($news);
        });
    }

    public function getId(string $slug)
    {
        return Cache::rememberForever("news:{$slug}:id", function () use ($slug) {
            return News::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()?->id;
        });
    }
}
