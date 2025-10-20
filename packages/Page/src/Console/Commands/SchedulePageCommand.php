<?php

namespace Packages\Page\Console\Commands;

use Illuminate\Console\Command;
use Packages\Page\Events\PagePublishedEvent;
use Packages\Page\Models\Page;

class SchedulePageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish pending pages';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Page::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function ($page) {
                $page->update(['status' => 'PUBLISHED']);
                event(new PagePublishedEvent($page));
            });

        $this->info('Pending pages checked');
    }
}
