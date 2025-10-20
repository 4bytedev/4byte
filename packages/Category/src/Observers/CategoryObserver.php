<?php

namespace Packages\Category\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Category\Models\Category;

class CategoryObserver
{
    /**
     * Handle the "updated" event for the Category model.
     */
    public function updated(Category $category): void
    {
        Cache::forget("category:{$category->id}");
    }

    /**
     * Handle the "deleted" event for the Category model.
     */
    public function deleted(Category $category): void
    {
        Cache::forget("category:{$category->slug}:id");
        Cache::forget("category:{$category->id}");
        Cache::forget("category:{$category->id}:articles");
        Cache::forget("category:{$category->id}:news");
        Cache::forget("category:{$category->id}:followers");
    }
}
