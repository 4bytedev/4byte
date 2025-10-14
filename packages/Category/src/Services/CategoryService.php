<?php

namespace Packages\Category\Services;

use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\Article;
use Packages\Category\Data\CategoryData;
use Packages\Category\Data\CategoryProfileData;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryFollow;
use Packages\Category\Models\CategoryProfile;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Packages\Tag\Models\Tag;

class CategoryService
{
    public function getData(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}", function () use ($categoryId) {
            $category = Category::select(['name', 'slug'])
                ->findOrFail($categoryId);

            return CategoryData::fromModel($category);
        });
    }

    public function getId(string $slug)
    {
        return Cache::rememberForever("category:{$slug}:id", function () use ($slug) {
            return Category::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    public function getProfileData(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}:profile", function () use ($categoryId) {
            $profile = CategoryProfile::where('category_id', $categoryId)
                ->select(['description', 'color'])
                ->firstOrFail();

            return CategoryProfileData::fromModel($profile);
        });
    }

    public function getArticlesCount(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}:articles", function () use ($categoryId) {
            return Article::whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            })->count();
        });
    }

    public function getNewsCount(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}:news", function () use ($categoryId) {
            return News::whereHas('categories', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            })->count();
        });
    }

    public function getFollowersCount(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}:followers", function () use ($categoryId) {
            return CategoryFollow::where('category_id', $categoryId)->count();
        });
    }

    public function checkFollowing(int $categoryId, ?int $userId)
    {
        if (! $userId) {
            return false;
        }

        return Cache::rememberForever("category:{$categoryId}:{$userId}:followed", function () use ($categoryId, $userId) {
            return CategoryFollow::where('user_id', $userId)->where('category_id', $categoryId)->exists();
        });
    }

    public function insertFollow(int $categoryId, int $userId)
    {
        CategoryFollow::create([
            'category_id' => $categoryId,
            'user_id' => $userId,
        ]);
        Cache::increment("category:{$categoryId}:followers");
        Cache::forever("category:{$categoryId}:{$userId}:followed", true);
    }

    public function deleteFollow(int $categoryId, int $userId)
    {
        $deleted = CategoryFollow::where('category_id', $categoryId)
            ->where('user_id', $userId)
            ->delete();
        if ($deleted) {
            Cache::decrement("category:{$categoryId}:followers");
            Cache::forever("category:{$categoryId}:{$userId}:followed", false);
        }

        return $deleted;
    }

    public function listTags(int $categoryId)
    {
        return Cache::rememberForever("category:{$categoryId}:tags", function () use ($categoryId) {
            $tags = Tag::whereHas('profile', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })->get();

            return TagData::collect($tags);
        });
    }
}
