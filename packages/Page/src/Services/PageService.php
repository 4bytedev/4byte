<?php

namespace Packages\Page\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Page\Data\PageData;
use Packages\Page\Models\Page;

class PageService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    /**
     * Retrieve page data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $pageId): PageData
    {
        $page = Cache::rememberForever("page:{$pageId}", function () use ($pageId) {
            return Page::query()
                ->where('status', 'PUBLISHED')
                ->select(['id', 'title', 'slug', 'content', 'excerpt', 'image', 'published_at', 'user_id'])
                ->findOrFail($pageId);
        });

        $user = $this->userService->getData($page->user_id);

        return PageData::fromModel($page, $user);
    }

    /**
     * Retrieve the ID of a page by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("page:{$slug}:id", function () use ($slug) {
            return Page::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
