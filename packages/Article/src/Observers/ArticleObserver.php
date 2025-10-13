<?php

namespace Packages\Article\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Packages\Article\Models\Article;
use Packages\Recommend\Services\GorseItem;
use Packages\Recommend\Services\GorseService;

class ArticleObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function saved(Article $article)
    {
        $gorseItem = new GorseItem(
            'article:'.$article->id,
            ['article', "user:{$article->user_id}"],
            $article->tags->pluck('id')
                ->map(fn ($id) => 'tag:'.$id)
                ->merge(
                    $article->categories->pluck('id')
                        ->map(fn ($id) => 'category:'.$id)
                )
                ->merge(['article', "user:{$article->user_id}"])
                ->all(),
            $article->slug,
            $article->status != 'PUBLISHED',
            Carbon::parse($article->published_at)->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    public function updating(Article $article)
    {
        if ($article->isDirty('image')) {
            $oldMedia = $article->getFirstMedia('article');
            if ($oldMedia) {
                $oldMedia->delete();
            }
        }
    }

    public function updated(Article $article)
    {
        Cache::forget("article:{$article->id}");
    }

    public function deleted(Article $article)
    {
        $this->gorse->deleteItem('article:'.$article->id);
        Cache::forget("article:{$article->slug}:id");
        Cache::forget("article:{$article->id}");
        Cache::forget("article:{$article->id}:likes");
        Cache::forget("article:{$article->id}:dislikes");
    }
}
