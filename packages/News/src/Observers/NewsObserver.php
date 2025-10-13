<?php

namespace Packages\News\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Packages\News\Models\News;
use Packages\Recommend\Services\GorseItem;
use Packages\Recommend\Services\GorseService;

class NewsObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function saved(News $news)
    {
        $gorseItem = new GorseItem(
            'news:'.$news->id,
            ['news', "user:{$news->user_id}"],
            $news->tags->pluck('id')
                ->map(fn ($id) => 'tag:'.$id)
                ->merge(
                    $news->categories->pluck('id')
                        ->map(fn ($id) => 'category:'.$id)
                )
                ->merge(['news', "user:{$news->user_id}"])
                ->all(),
            $news->slug,
            $news->status != 'PUBLISHED',
            Carbon::parse($news->published_at)->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    public function updating(News $news)
    {
        if ($news->isDirty('image')) {
            $oldPath = $news->getOriginal('image');

            if ($oldPath && Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }
    }

    public function updated(News $news)
    {
        $this->gorse->deleteItem('news:'.$news->id);
        Cache::forget("news:{$news->id}");
    }

    public function deleted(News $news)
    {
        $this->gorse->deleteItem('news:'.$news->id);
        Cache::forget("news:{$news->slug}:id");
        Cache::forget("news:{$news->id}");
    }
}
