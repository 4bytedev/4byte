<?php

namespace Packages\Recommend\Services;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Packages\Article\Models\Article;
use Packages\Article\Services\ArticleService;
use Packages\Category\Services\CategoryService;
use Packages\Entry\Services\EntryService;
use Packages\News\Services\NewsService;
use Packages\React\Models\Save;
use Packages\React\Services\ReactService;
use Packages\Tag\Services\TagService;

class FeedService
{
    protected GorseService $gorseService;

    protected ArticleService $articleService;

    protected NewsService $newsService;

    protected EntryService $entryService;

    protected ReactService $reactService;

    protected UserService $userService;

    protected TagService $tagService;

    protected CategoryService $categoryService;

    public function __construct()
    {
        $this->gorseService    = app(GorseService::class);
        $this->articleService  = app(ArticleService::class);
        $this->newsService     = app(NewsService::class);
        $this->entryService    = app(EntryService::class);
        $this->reactService    = app(ReactService::class);
        $this->userService     = app(UserService::class);
        $this->tagService      = app(TagService::class);
        $this->categoryService = app(CategoryService::class);
    }

    /**
     * Get popular articles based on likes count.
     *
     * @return array<int, \Packages\Article\Data\ArticleData>
     */
    public function articles(): array
    {
        return Cache::remember('feed:articles', 60 * 60 * 24, function () {
            return Article::select(['id'])
                ->withCount('likes')
                ->where('status', 'PUBLISHED')
                ->orderBy('likes_count', 'desc')
                ->take(7)
                ->get()->map(function (Article $article) {
                    return $this->articleService->getData($article->id);
                })->all();
        });
    }

    /**
     * Get popular categories based on content count.
     *
     * @return array<int, array{name: string, slug: string|null, total: int}>
     */
    public function categories(): array
    {
        return Cache::remember('feed:categories', 60 * 60 * 24, function () {
            $categoryTotals = $this->getTotals('category');

            return $categoryTotals->map(function ($cat) {
                return [
                    'data'  => $this->categoryService->getData($cat->category_id),
                    'total' => $cat->total,
                ];
            })->all();
        });
    }

    /**
     * Get popular tags based on content count.
     *
     * @return array<int, array{name: string, slug: string|null, total: int}>
     */
    public function tags()
    {
        return Cache::remember('feed:tags', 60 * 60 * 24, function () {
            $tagTotals = $this->getTotals('tag');

            return $tagTotals->map(function ($tag) {
                return [
                    'data'  => $this->tagService->getData($tag->tag_id),
                    'total' => $tag->total,
                ];
            })->all();
        });
    }

    /**
     * Build filters for recommendation query.
     *
     * @return array<int, string>
     */
    public function buildFilters(Request $request): array
    {
        $filters = [];

        if ($request->tab && $request->tab !== 'all') {
            $filters[] = $request->tab;
        }

        $tagId = $request->tag && $request->tag !== 'all' ? $this->tagService->getId($request->tag) : null;
        if ($tagId) {
            $filters[] = "tag:{$tagId}";
        }

        $categoryId = $request->category && $request->category !== 'all' ? $this->categoryService->getId($request->category) : null;
        if ($categoryId) {
            $filters[] = "category:{$categoryId}";
        }

        $userId = $request->user && $request->user !== 'all' ? $this->userService->getId($request->user) : null;
        if ($userId) {
            $filters[] = "user:{$userId}";
        }

        return $filters;
    }

    /**
     * Get content for a specific tab.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTabContents(string $tab, ?int $userId): array
    {
        if ($tab === 'drafts' && $userId) {
            return Article::where('status', 'DRAFT')
                ->where('user_id', $userId)
                ->select(['title', 'slug'])
                ->get()->map(fn ($article) => [
                    'title' => $article->title,
                    'slug'  => $article->slug,
                    'type'  => 'draft',
                ])->toArray();
        }
        if ($tab === 'saves' && $userId) {
            return Article::whereIn(
                'id',
                Save::where('user_id', $userId)
                    ->where('saveable_type', Article::class)
                    ->pluck('saveable_id')
            )->get()
                ->map(fn ($article) => $this->articleService->getData($article->id))
                ->toArray();
        }

        return [];
    }

    /**
     * Get personalized recommendations for a logged-in user.
     *
     * @param array<int, string> $filters
     *
     * @return array<int, string>|null
     */
    public function getPersonalizedRecommendations(int $userId, array $filters, int $limit, int $offset): ?array
    {
        if (count($filters) === 0) {
            return $this->gorseService->getRecommend((string) $userId, $limit, $offset);
        }

        return $this->gorseService->getRecommendByCategory((string) $userId, $limit, $offset, $filters);
    }

    /**
     * Get recommendations for a non-logged-in user.
     *
     * @param array<int, string> $filters
     *
     * @return array<int, string>|null
     */
    public function getNonPersonalizedRecommendations(string $name, array $filters, int $limit, int $offset): ?array
    {
        if (count($filters) === 0) {
            return $this->gorseService->getNonPersonalizedRecommend($name, $limit, $offset);
        }

        return $this->gorseService->getNonPersonalizedRecommendByCategory($name, $limit, $offset, $filters);
    }

    /**
     * Resolve recommendation IDs into full content arrays.
     *
     * @param array<int, string|array{Id: string}> $recommendations
     *
     * @return array<int, mixed>
     */
    public function resolveContents(array $recommendations): array
    {
        $contents = [];

        foreach ($recommendations as $recommend) {
            $recommendId = '';
            if (is_array($recommend)) {
                $recommendId = $recommend['Id'];
            } else {
                $recommendId = $recommend;
            }

            $pos = strpos($recommendId, ':');
            if ($pos === false) {
                continue;
            }

            $type = trim(substr($recommendId, 0, $pos));
            $id   = trim(substr($recommendId, $pos + 1));

            $content = $this->getContent($type, (int) $id);
            if ($content !== null) {
                $contents[] = $content;
            }
        }

        return $contents;
    }

    /**
     * Get single content by type and ID.
     *
     * @return mixed|null
     */
    private function getContent(string $type, int $id)
    {
        try {
            return match ($type) {
                'article' => $this->articleService->getData($id),
                'news'    => $this->newsService->getData($id),
                'entry'   => $this->entryService->getData($id),
                'comment' => $this->reactService->getComment($id),
                default   => null,
            };
        } catch (\Throwable $th) {
            logger()->error('Invalid recommended content', [
                'type' => $type,
                'id'   => $id,
                'th'   => $th,
            ]);

            return;
        }
    }

    /**
     * Get content totals for a type (category or tag).
     *
     * @return Collection<int, \stdClass>
     */
    private function getTotals(string $type): Collection
    {
        $articleCounts = DB::table('article_' . $type)
            ->select($type . '_id', DB::raw('COUNT(*) as count'))
            ->groupBy($type . '_id');

        $newsCounts = DB::table('news_' . $type)
            ->select($type . '_id', DB::raw('COUNT(*) as count'))
            ->groupBy($type . '_id');

        return DB::query()
            ->fromSub($articleCounts->unionAll($newsCounts), $type . '_counts')
            ->select($type . '_id', DB::raw('SUM(count) as total'))
            ->groupBy($type . '_id')
            ->orderByDesc('total')
            ->limit(7)
            ->get();
    }
}
