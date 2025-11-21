<?php

namespace Packages\Course\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\Course\Events\LessonPublishedEvent;
use Packages\Course\Notifications\LessonPublishedNotification;

class LessonPublishedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(LessonPublishedEvent $event): void
    {
        $lesson = $event->lesson;

        $lesson->chapter->course->user->notify(new LessonPublishedNotification($lesson));
        $lesson->user->notify(
            Notification::make()
                ->title(__('course::messages.lessom_published_title'))
                ->success()
                ->body(__('course::messages.lesson_published_body', ['title' => $lesson->title]))
                ->actions([
                    Action::make('view')
                        ->label(__('course::messages.view_lesson'))
                        ->url(route('course.page', ['slug' => $lesson->chapter->course->slug, 'page' => $lesson->slug]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
