<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected SeoService $seoService;

    public function __construct()
    {
        $this->seoService = app(SeoService::class);
    }

    public function view(Request $request)
    {
        return Inertia::render('Home/Detail')->withViewData(['seo' => $this->seoService->getHomeSEO()]);
    }
}
