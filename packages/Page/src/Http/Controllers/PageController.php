<?php

namespace Packages\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Packages\Page\Services\PageService;

class PageController extends Controller
{
    protected PageService $pageService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->pageService = app(PageService::class);
        $this->seoService  = app(SeoService::class);
    }

    /**
     * Display a page detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug   = $request->route('slug');
        $pageId = $this->pageService->getId($slug);
        $page   = $this->pageService->getData($pageId);

        return Inertia::render('Page/Detail', [
            'page' => $page,
        ])->withViewData(['seo' => $this->seoService->getPageSEO($page, $page->user)]);
    }
}
