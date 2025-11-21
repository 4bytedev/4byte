<?php

namespace Packages\Course\Console\Commands;

use Illuminate\Console\Command;
use Packages\Course\Events\LessonPublishedEvent;
use Packages\Course\Models\CourseLesson;

class ScheduleLessonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lesson:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish pending lessons';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CourseLesson::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function ($lesson) {
                $lesson->update(['status' => 'PUBLISHED']);
                event(new LessonPublishedEvent($lesson));
            });

        $this->info('Pending courses checked');
    }
}
