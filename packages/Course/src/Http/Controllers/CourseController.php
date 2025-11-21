<?php

namespace Packages\Course\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a article detail page.
     */
    public function view(Request $request)
    {
        return Inertia::render("Course/Detail");
    }

    /**
     * Display a article detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function page(Request $request)
    {
        return Inertia::render("Course/Page");
    }
}
