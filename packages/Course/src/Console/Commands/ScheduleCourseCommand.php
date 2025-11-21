<?php

namespace Packages\Course\Console\Commands;

use Illuminate\Console\Command;
use Packages\Course\Events\CoursePublishedEvent;
use Packages\Course\Models\Course;

class ScheduleCourseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish pending courses';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Course::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function ($course) {
                $course->update(['status' => 'PUBLISHED']);
                event(new CoursePublishedEvent($course));
            });

        $this->info('Pending courses checked');
    }
}
