<?php

namespace Packages\Tag\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\Article;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Packages\Tag\Data\TagProfileData;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;

class TagService
{
    /**
     * Retrieve tag data by its ID.
     *
     * @param int $tagId
     *
     * @return TagData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $tagId): TagData
    {
        $tag = Cache::rememberForever("tag:{$tagId}", function () use ($tagId) {
            return Tag::select(['id', 'name', 'slug'])
                ->findOrFail($tagId);
        });

        return TagData::fromModel($tag);
    }

    /**
     * Retrieve the ID of a tag by its slug.
     *
     * @param string $slug
     *
     * @return int
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("tag:{$slug}:id", function () use ($slug) {
            return Tag::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Retrieve profile information for a given tag.
     *
     * @param int $tagId
     *
     * @return TagProfileData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfileData(int $tagId): TagProfileData
    {
        return Cache::rememberForever("tag:{$tagId}:profile", function () use ($tagId) {
            $profile = TagProfile::where('tag_id', $tagId)
                ->select(['id', 'description', 'color'])
                ->with('categories:name,slug')
                ->firstOrFail();

            return TagProfileData::fromModel($profile);
        });
    }

    /**
     * Count the number of news posts associated with a tag.
     *
     * @param int $tagId
     *
     * @return int
     */
    public function getArticlesCount(int $tagId): int
    {
        return Cache::remember("tag:{$tagId}:articles", 60 * 60 * 24, function () use ($tagId) {
            return Article::whereHas('tags', function ($q) use ($tagId) {
                $q->where('id', $tagId);
            })->count();
        });
    }

    /**
     * Get the number of news for a tag by id.
     *
     * @param int $tagId
     *
     * @return int
     */
    public function getNewsCount(int $tagId): int
    {
        return Cache::remember("tag:{$tagId}:news", 60 * 60 * 24, function () use ($tagId) {
            return News::whereHas('tags', function ($q) use ($tagId) {
                $q->where('id', $tagId);
            })->count();
        });
    }

    /**
     * Retrieve a list of tags related to a specific category.
     *
     * @param int $tagId
     *
     * @return Collection<int, TagData>
     */
    public function listRelated(int $tagId): Collection
    {
        return Cache::rememberForever("tag:{$tagId}:related", function () use ($tagId) {
            $tagProfile = TagProfile::with('categories')->where('tag_id', $tagId)->first();

            if (! $tagProfile) {
                return collect();
            }

            $categoryIds = $tagProfile->categories->pluck('id')->toArray();

            if (count($categoryIds) === 0) {
                return collect();
            }

            $relatedTags = Tag::whereHas('profile.categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
                ->where('id', '!=', $tagId)
                ->distinct()
                ->get();

            return TagData::collect($relatedTags);
        });
    }
}
