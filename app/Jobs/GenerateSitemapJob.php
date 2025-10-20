<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapJob implements ShouldQueue
{
    use Queueable;

    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sitemap    = Sitemap::create();
        $totalItems = 1;

        $sitemap->add(Url::create(route('home.view'))
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setLastModificationDate(Carbon::now()));

        foreach (User::all() as $user) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('user.view', ['username' => $user->username]))
                ->setPriority(0.5)
                ->setLastModificationDate($user->updated_at));
        }

        foreach (\Packages\Article\Models\Article::where('status', 'PUBLISHED')->get() as $article) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('article.view', ['slug' => $article->slug]))
                ->setPriority(0.7)
                ->setLastModificationDate($article->updated_at));
        }

        foreach (\Packages\Entry\Models\Entry::get() as $entry) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('entry.view', ['slug' => $entry->slug]))
                ->setPriority(0.6)
                ->setLastModificationDate($entry->created_at));
        }

        foreach (\Packages\News\Models\News::where('status', 'PUBLISHED')->get() as $news) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('news.view', ['slug' => $news->slug]))
                ->setPriority(0.6)
                ->setLastModificationDate($news->updated_at));
        }

        foreach (\Packages\Page\Models\Page::where('status', 'PUBLISHED')->get() as $page) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('page.view', ['slug' => $page->slug]))
                ->setPriority(0.5)
                ->setLastModificationDate($page->updated_at));
        }

        foreach (\Packages\Tag\Models\Tag::all() as $tag) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('tag.view', ['slug' => $tag->slug]))
                ->setPriority(0.4)
                ->setLastModificationDate($tag->updated_at));
        }

        foreach (\Packages\Category\Models\Category::all() as $cat) {
            $totalItems += 1;
            $sitemap->add(Url::create(route('category.view', ['slug' => $cat->slug]))
                ->setPriority(0.4)
                ->setLastModificationDate($cat->updated_at));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $user = User::find($this->userId);
        $user->notify(
            Notification::make()
                ->title('Sitemap Generated!')
                ->success()
                ->body("Sitemap successfully generated. Totally \"{$totalItems}\" urls generated.")
                ->actions([
                    Action::make('view')
                        ->label('View Sitemap')
                        ->url(url('/sitemap.xml'))
                        ->openUrlInNewTab()
                        ->button()
                        ->markAsRead(),
                ])
                ->toDatabase()
        );
    }
}
