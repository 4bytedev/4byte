<?php

namespace Packages\Tag\Services;

use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\Article;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Packages\Tag\Data\TagProfileData;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;

class TagService
{
    public function getData(int $tagId)
    {
        $tag = Cache::rememberForever("tag:{$tagId}", function () use ($tagId) {
            return Tag::select(['id', 'name', 'slug'])
                ->findOrFail($tagId);
        });

        return TagData::fromModel($tag);
    }

    public function getId(string $slug)
    {
        return Cache::rememberForever("tag:{$slug}:id", function () use ($slug) {
            return Tag::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    public function getProfileData(int $tagId)
    {
        return Cache::rememberForever("tag:{$tagId}:profile", function () use ($tagId) {
            $profile = TagProfile::where('tag_id', $tagId)
                ->select(['description', 'color', 'category_id'])
                ->firstOrFail();

            return TagProfileData::fromModel($profile);
        });
    }

    public function getArticlesCount(int $tagId)
    {
        return Cache::remember("tag:{$tagId}:articles", 60 * 60 * 24, function () use ($tagId) {
            return Article::whereHas('tags', function ($q) use ($tagId) {
                $q->where('id', $tagId);
            })->count();
        });
    }

    public function getNewsCount(int $tagId)
    {
        return Cache::remember("tag:{$tagId}:news", 60 * 60 * 24, function () use ($tagId) {
            return News::whereHas('tags', function ($q) use ($tagId) {
                $q->where('id', $tagId);
            })->count();
        });
    }
}
