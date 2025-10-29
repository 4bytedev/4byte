<?php

namespace Packages\Search\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Search\Services\SearchService;

class SearchController extends Controller
{
    protected SearchService $searchService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->searchService = app(SearchService::class);
        $this->seoService    = app(SeoService::class);
    }

    /**
     * Display a search page.
     */
    public function view(Request $request): Response
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);

        $q = $request->input('q');

        $results = $this->searchService->search($q);

        return Inertia::render('Search/Detail', [
            'q'       => $q,
            'results' => $results,
        ])->withViewData(['seo' => $this->seoService->getSearchSEO($q)]);
    }

    /**
     * Search accross searchable models.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);

        $q = $request->input('q');

        $results = $this->searchService->search($q);

        return response()->json($results);
    }
}
