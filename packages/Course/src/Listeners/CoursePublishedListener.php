<?php

namespace Packages\Course\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\Course\Events\CoursePublishedEvent;
use Packages\Course\Notifications\CoursePublishedNotification;

class CoursePublishedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(CoursePublishedEvent $event): void
    {
        $course = $event->course;

        $course->user->notify(new CoursePublishedNotification($course));
        $course->user->notify(
            Notification::make()
                ->title(__('course::messages.course_published_title'))
                ->success()
                ->body(__('course::messages.course_published_body', ['title' => $course->title]))
                ->actions([
                    Action::make('view')
                        ->label(__('course::messages.view_course'))
                        ->url(route('course.view', ['slug' => $course->slug]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
