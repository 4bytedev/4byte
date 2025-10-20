<?php

namespace Packages\Category\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\Article;
use Packages\Category\Data\CategoryData;
use Packages\Category\Data\CategoryProfileData;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Packages\Tag\Models\Tag;

class CategoryService
{
    /**
     * Retrieve category data by its ID.
     *
     * @param int $categoryId
     *
     * @return CategoryData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $categoryId): CategoryData
    {
        $category = Cache::rememberForever("category:{$categoryId}", function () use ($categoryId) {
            return Category::select(['id', 'name', 'slug'])
                ->findOrFail($categoryId);
        });

        return CategoryData::fromModel($category);
    }

    /**
     * Retrieve the ID of a category by its slug.
     *
     * @param string $slug
     *
     * @return int
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("category:{$slug}:id", function () use ($slug) {
            return Category::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Retrieve profile information for a given category.
     *
     * @param int $categoryId
     *
     * @return CategoryProfileData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfileData(int $categoryId): CategoryProfileData
    {
        return Cache::rememberForever("category:{$categoryId}:profile", function () use ($categoryId) {
            $profile = CategoryProfile::where('category_id', $categoryId)
                ->select(['description', 'color'])
                ->firstOrFail();

            return CategoryProfileData::fromModel($profile);
        });
    }

    /**
     * Count the number of news posts associated with a category.
     *
     * @param int $categoryId
     *
     * @return int
     */
    public function getArticlesCount(int $categoryId): int
    {
        return Cache::rememberForever("category:{$categoryId}:articles", function () use ($categoryId) {
            return Article::whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            })->count();
        });
    }

    /**
     * Get the number of news for a category by id.
     *
     * @param int $categoryId
     *
     * @return int
     */
    public function getNewsCount(int $categoryId): int
    {
        return Cache::rememberForever("category:{$categoryId}:news", function () use ($categoryId) {
            return News::whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            })->count();
        });
    }

    /**
     * Retrieve a list of tags related to a specific category.
     *
     * @param int $categoryId
     *
     * @return Collection<int, TagData>
     */
    public function listTags(int $categoryId): Collection
    {
        return Cache::rememberForever("category:{$categoryId}:tags", function () use ($categoryId) {
            $tags = Tag::whereHas('profile.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })->get();

            return TagData::collect($tags);
        });
    }
}
