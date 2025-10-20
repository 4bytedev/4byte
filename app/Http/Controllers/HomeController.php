<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    protected SeoService $seoService;

    public function __construct()
    {
        $this->seoService = app(SeoService::class);
    }

    /**
     * Display a homepage.
     */
    public function view(): Response
    {
        return Inertia::render('Home/Detail')->withViewData(['seo' => $this->seoService->getHomeSEO()]);
    }
}
