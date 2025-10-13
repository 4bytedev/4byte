<?php

namespace Packages\Category\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Category\Models\Category;

class CategoryProfileObserver
{
    public function updated(Category $category)
    {
        Cache::forget("category:{$category->id}:profile");
    }

    public function deleted(Category $category)
    {
        Cache::forget("category:{$category->id}:profile");
    }
}
