<?php

namespace Packages\Recommend\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Packages\Article\Models\Article;
use Packages\Entry\Models\Entry;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Follow;
use Packages\React\Models\Like;
use Packages\React\Models\Save;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Classes\GorseUser;
use Packages\Recommend\Services\GorseService;

class UploadRecommendations extends Command
{
    protected $signature = 'recommendation:upload';
    protected $description = 'Upload items and reactions to recommendation engine';
    protected GorseService $gorseService;

    public function handle()
    {
        $this->gorseService = app(GorseService::class);

        $this->uploadUsers();
        $this->uploadArticles();
        $this->uploadEntries();
        $this->uploadReactions();
        $this->uploadFollows();

        $this->info('✅ All recommendations uploaded successfully!');
    }

    protected function uploadUsers()
    {
        User::all()->each(function ($user) {
            $this->gorseService->insertUser(
                new GorseUser((string) $user->id, ['article', 'entry', 'news'], [], $user->username)
            );
        });
        $this->info('✅ Users uploaded successfully!');
    }

    protected function uploadArticles()
    {
        Article::all()->each(function ($article) {
            $tags = $article->tags->pluck('id')->map(fn ($id) => 'tag:'.$id);
            $categories = $article->categories->pluck('id')->map(fn ($id) => 'category:'.$id);

            $this->gorseService->insertItem(
                new GorseItem(
                    'article:'.$article->id,
                    ['article', "user:{$article->user_id}"],
                    $tags->merge($categories)->merge(['article', "user:{$article->user_id}"])->all(),
                    $article->slug,
                    $article->status !== 'PUBLISHED',
                    Carbon::parse($article->published_at)->toDateTimeString()
                )
            );
        });
        $this->info('✅ Articles uploaded successfully!');
    }

    protected function uploadEntries()
    {
        Entry::all()->each(function ($entry) {
            $this->gorseService->insertItem(
                new GorseItem(
                    'entry:'.$entry->id,
                    ['entry', "user:{$entry->user_id}"],
                    [],
                    '',
                    false,
                    Carbon::now()->toDateTimeString()
                )
            );
        });
        $this->info('✅ Entries uploaded successfully!');
    }

    protected function uploadReactions()
    {
        $reactionTypes = [
            'like' => Like::class,
            'dislike' => Dislike::class,
            'comment' => Comment::class,
            'save' => Save::class,
        ];

        foreach ($reactionTypes as $type => $model) {
            $model::all()->each(function ($item) use ($type) {
                $this->gorseService->insertFeedback(
                    new GorseFeedback(
                        $type,
                        (string) $item->user_id,
                        strtolower(class_basename($item->{$type === 'like' ? 'likeable' : ($type === 'dislike' ? 'dislikeable' : ($type === 'comment' ? 'commentable' : 'saveable'))})) . ':' . $item->{$type === 'like' ? 'likeable' : ($type === 'dislike' ? 'dislikeable' : ($type === 'comment' ? 'commentable' : 'saveable'))}->id,
                        '',
                        Carbon::now()
                    )
                );
            });
            $this->info('✅ ' . ucfirst($type) . 's uploaded successfully!');
        }
    }

    protected function uploadFollows()
    {
        Follow::all()->each(function ($follow) {
            $gorseUser = $this->gorseService->getUser((string) $follow->follower_id);
            $gorseUser->addLabel(strtolower(class_basename($follow->followable)) . ':' . $follow->followable->id);
            $this->gorseService->updateUser($gorseUser);
        });
        $this->info('✅ Follows uploaded successfully!');
    }
}
