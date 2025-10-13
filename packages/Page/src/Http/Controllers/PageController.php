<?php

namespace Packages\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Packages\Page\Services\PageService;

class PageController extends Controller
{
    protected PageService $pageService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->pageService = app(PageService::class);
        $this->seoService = app(SeoService::class);
    }

    public function view(Request $request)
    {
        $slug = $request->route('slug');
        $pageId = $this->pageService->getId($slug);
        $page = $this->pageService->getData($pageId);

        return Inertia::render('Page/Detail', [
            'page' => $page,
        ])->withViewData(['seo' => $this->seoService->getPageSEO($page, $page->user)]);
    }
}
