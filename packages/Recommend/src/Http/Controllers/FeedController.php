<?php

namespace Packages\Recommend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Packages\Recommend\Http\Requests\FeedRequest;
use Packages\Recommend\Services\FeedService;

class FeedController extends Controller
{
    protected FeedService $feedService;

    public function __construct()
    {
        $this->feedService = app(FeedService::class);
    }

    public function data()
    {
        $topCategories = $this->feedService->categories();

        $topTags = $this->feedService->tags();

        $topArticles = $this->feedService->articles();

        return response()->json([
            'categories' => $topCategories,
            'tags' => $topTags,
            'articles' => $topArticles,
        ])->header('Cache-Control', 'public, max-age=86400, immutable');
    }

    public function feed(FeedRequest $request)
    {
        $userId = Auth::id() ?? null;
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $tab = $request->get('tab', 'all');

        if ($tab !== 'all') {
            $tabContents = $this->feedService->getTabContents($tab, $userId);

            return response()->json($tabContents);
        }

        $filters = $this->feedService->buildFilters($request);
        $recommendations = [];
        if (Auth::check()) {
            $recommendations = $this->feedService->getPersonalizedRecommendations($userId, $filters, $limit, ($page - 1) * $limit);
        } else {
            $recommendations = $this->feedService->getNonPersonalizedRecommendations('trending', $filters, $limit, ($page - 1) * $limit);
        }
        if (! $recommendations) {
            return response()->json([]);
        }

        $contents = $this->feedService->resolveContents($recommendations);

        return response()->json($contents);
    }
}
