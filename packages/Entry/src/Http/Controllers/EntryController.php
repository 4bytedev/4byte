<?php

namespace Packages\Entry\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Entry\Services\EntryService;
use Packages\Recommend\Services\FeedService;

class EntryController extends Controller
{
    protected EntryService $entryService;

    protected SeoService $seoService;

    protected FeedService $feedService;

    public function __construct()
    {
        $this->entryService = app(EntryService::class);
        $this->seoService   = app(SeoService::class);
        $this->feedService  = app(FeedService::class);
    }

    /**
     * Display a entry detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug    = $request->route('slug');
        $entryId = $this->entryService->getId($slug);
        $entry   = $this->entryService->getData($entryId);

        return Inertia::render('Entry/Detail', [
            'entry' => $entry,
        ])->withViewData(['seo' => $this->seoService->getEntrySEO($entry, $entry->user)]);
    }
}
