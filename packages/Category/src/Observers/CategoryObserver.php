<?php

namespace Packages\Category\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Category\Models\Category;

class CategoryObserver
{
    public function updated(Category $category)
    {
        Cache::forget("category:{$category->id}");
    }

    public function deleted(Category $category)
    {
        Cache::forget("category:{$category->slug}:id");
        Cache::forget("category:{$category->id}");
        Cache::forget("category:{$category->id}:articles");
        Cache::forget("category:{$category->id}:news");
        Cache::forget("category:{$category->id}:followers");
    }
}
