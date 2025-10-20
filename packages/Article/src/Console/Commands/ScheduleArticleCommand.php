<?php

namespace Packages\Article\Console\Commands;

use Illuminate\Console\Command;
use Packages\Article\Events\ArticlePublishedEvent;
use Packages\Article\Models\Article;

class ScheduleArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish pending articles';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Article::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function ($article) {
                $article->update(['status' => 'PUBLISHED']);
                event(new ArticlePublishedEvent($article));
            });

        $this->info('Pending articles checked');
    }
}
