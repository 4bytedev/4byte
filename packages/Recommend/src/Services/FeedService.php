<?php

namespace Packages\Recommend\Services;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleSave;
use Packages\Article\Services\ArticleService;
use Packages\Category\Models\Category;
use Packages\Category\Services\CategoryService;
use Packages\News\Services\NewsService;
use Packages\Tag\Models\Tag;
use Packages\Tag\Services\TagService;

class FeedService
{
    protected GorseService $gorseService;

    protected ArticleService $articleService;

    protected NewsService $newsService;

    protected UserService $userService;

    protected TagService $tagService;

    protected CategoryService $categoryService;

    public function __construct()
    {
        $this->gorseService = app(GorseService::class);
        $this->articleService = app(ArticleService::class);
        $this->newsService = app(NewsService::class);
        $this->userService = app(UserService::class);
        $this->tagService = app(TagService::class);
        $this->categoryService = app(CategoryService::class);
    }

    public function articles()
    {
        return Cache::remember('feed:articles', 60 * 60 * 24, function () {
            $articles = Article::select('id', 'title', 'slug', 'excerpt', 'user_id')
                ->with('user:id,name,username')
                ->withCount('likes')
                ->where('status', 'PUBLISHED')
                ->orderBy('likes_count', 'desc')
                ->take(7)
                ->get()
                ->map(function (Article $article) {
                    /** @var \App\Models\User $article->user */
                    return [
                        'title' => $article->title,
                        'slug' => $article->slug,
                        'excerpt' => $article->excerpt,
                        'likes_count' => $article->likes_count,
                        'user' => [
                            'name' => $article->user->name,
                            'username' => $article->user->username,
                            'avatar' => $article->user->getAvatarImage(),
                        ],
                    ];
                });

            return $articles;
        });
    }

    public function categories()
    {
        return Cache::remember('feed:categories', 60 * 60 * 24, function () {
            $categoryTotals = $this->getTotals('category');

            $categoryDetails = Category::whereIn('id', $categoryTotals->pluck('category_id'))->get()->keyBy('id');

            $topCategories = $categoryTotals->map(function ($cat) use ($categoryDetails) {
                return [
                    'name' => $categoryDetails[$cat->category_id]->name ?? 'Unknown',
                    'slug' => $categoryDetails[$cat->category_id]->slug ?? null,
                    'total' => $cat->total,
                ];
            });

            return $topCategories;
        });
    }

    public function tags()
    {
        return Cache::remember('sidebar:tags', 60 * 60 * 24, function () {
            $tagTotals = $this->getTotals('tag');

            $tagDetails = Tag::whereIn('id', $tagTotals->pluck('tag_id'))->get()->keyBy('id');

            $topTags = $tagTotals->map(function ($tag) use ($tagDetails) {
                return [
                    'name' => $tagDetails[$tag->tag_id]->name ?? 'Unknown',
                    'slug' => $tagDetails[$tag->tag_id]->slug ?? null,
                    'total' => $tag->total,
                ];
            });

            return $topTags;
        });
    }

    private function getTotals(string $type)
    {
        $articleCounts = DB::table('article_'.$type)
            ->select($type.'_id', DB::raw('COUNT(*) as count'))
            ->groupBy($type.'_id');

        $newsCounts = DB::table('news_'.$type)
            ->select($type.'_id', DB::raw('COUNT(*) as count'))
            ->groupBy($type.'_id');

        $totals = DB::query()
            ->fromSub($articleCounts->unionAll($newsCounts), $type.'_counts')
            ->select($type.'_id', DB::raw('SUM(count) as total'))
            ->groupBy($type.'_id')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        return $totals;
    }

    public function buildFilters(Request $request): array
    {
        $filters = [];

        if ($request->tab && $request->tab !== 'all') {
            $filters[] = $request->tab;
        }

        if ($request->tag && $request->tag !== 'all') {
            if ($tagId = $this->tagService->getId($request->tag)) {
                $filters[] = "tag:{$tagId}";
            }
        }

        if ($request->category && $request->category !== 'all') {
            if ($categoryId = $this->categoryService->getId($request->category)) {
                $filters[] = "category:{$categoryId}";
            }
        }

        if ($request->user && $request->user !== 'all') {
            if ($userId = $this->userService->getId($request->user)) {
                $filters[] = "user:{$userId}";
            }
        }

        return $filters;
    }

    public function getTabContents(string $tab, ?int $userId): array
    {
        if ($tab === 'drafts' && $userId) {
            return Article::where('status', 'DRAFT')
                ->where('user_id', $userId)
                ->select(['title', 'slug'])
                ->get()
                ->map(fn ($article) => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'type' => 'draft',
                ])
                ->toArray();
        }

        if ($tab === 'saves' && $userId) {
            return Article::whereIn(
                'id',
                ArticleSave::where('user_id', $userId)->pluck('article_id')
            )->get()
                ->map(fn ($article) => $this->articleService->getData($article->id))
                ->toArray();
        }

        return [];
    }

    public function getRecommendations(?int $userId, array $filters, int $limit, int $offset): array
    {
        if (empty($filters)) {
            return $this->gorseService->getRecommend($userId ?? 'guest', $limit, $offset);
        }

        return $this->gorseService->getRecommendByCategory($userId ?? 'guest', $limit, $offset, $filters);
    }

    public function resolveContents(array $recommendations): array
    {
        $contents = [];

        foreach ($recommendations as $recommend) {
            $pos = strpos($recommend, ':');
            if ($pos === false) {
                continue;
            }

            $type = trim(substr($recommend, 0, $pos));
            $id = trim(substr($recommend, $pos + 1));

            try {
                if ($type === 'article') {
                    $contents[] = $this->articleService->getData((int) $id);
                } elseif ($type === 'news') {
                    $contents[] = $this->newsService->getData((int) $id);
                }
            } catch (\Throwable $th) {
                logger()->error('Invalid recommended content', ['type' => $type, 'id' => $id, 'th' => $th]);
            }
        }

        return $contents;
    }
}
