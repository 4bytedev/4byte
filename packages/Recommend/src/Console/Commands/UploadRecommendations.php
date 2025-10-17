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
use Packages\Recommend\Services\Feedback;
use Packages\Recommend\Services\GorseItem;
use Packages\Recommend\Services\GorseService;
use Packages\Recommend\Services\GorseUser;

class UploadRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendation:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload items and reactions to recommendation engine';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gorseService = app(GorseService::class);

        $users = User::all();

        foreach ($users as $user) {
            $gorseUser = new GorseUser((string) $user->id, ['article', 'entry', 'news'], [], $user->username);
            $gorseService->insertUser($gorseUser);
        }

        $this->info('✅ Users uploaded successfully!');

        $articles = Article::all();

        foreach ($articles as $article) {
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
            $gorseService->insertItem($gorseItem);
        }

        $this->info('✅ Articles uploaded successfully!');

        $entries = Entry::all();

        foreach ($entries as $entry) {
            $gorseItem = new GorseItem(
                'entry:'.$entry->id,
                ['entry', "user:{$entry->user_id}"],
                [],
                '',
                false,
                Carbon::now()->toDateTimeString()
            );
            $gorseService->insertItem($gorseItem);
        }

        $this->info('✅ Entries uploaded successfully!');

        $likes = Like::all();

        foreach ($likes as $like) {
            $feedback = new Feedback('like', (string) $like->user_id, strtolower(class_basename($like->likeable)).':'.$like->likeable->id, '', Carbon::now());
            $gorseService->insertFeedback($feedback);
        }

        $this->info('✅ Likes uploaded successfully!');

        $dislikes = Dislike::all();

        foreach ($dislikes as $dislike) {
            $feedback = new Feedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)).':'.$dislike->dislikeable->id, '', Carbon::now());
            $gorseService->insertFeedback($feedback);
        }

        $this->info('✅ Dislikes uploaded successfully!');

        $comments = Comment::all();

        foreach ($comments as $comment) {
            $feedback = new Feedback('comment', (string) $comment->user_id, strtolower(class_basename($comment->commentable)).':'.$comment->commentable->id, '', Carbon::now());
            $gorseService->insertFeedback($feedback);
        }

        $this->info('✅ Comments uploaded successfully!');

        $saves = Save::all();

        foreach ($saves as $save) {
            $feedback = new Feedback('save', (string) $save->user_id, strtolower(class_basename($save->saveable)).':'.$save->saveable->id, '', Carbon::now());
            $gorseService->insertFeedback($feedback);
        }

        $this->info('✅ Saves uploaded successfully!');

        $follows = Follow::all();

        foreach ($follows as $follow) {
            $gorseUser = $gorseService->getUser((string) $follow->follower_id);
            $gorseUser->labels[] = strtolower(class_basename($follow->followable)).':'.$follow->followable->id;
            $gorseService->updateUser($gorseUser);
        }

        $this->info('✅ Follows uploaded successfully!');

        $this->info('✅ All recommendations uploaded successfully!');
    }
}
