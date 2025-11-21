<?php

namespace Packages\Course\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Packages\Course\Models\CourseLesson;

class LessonPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public CourseLesson $lesson;

    /**
     * Create a new notification instance.
     */
    public function __construct(CourseLesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->subject(__('course::messages.lesson_published_title'))
            ->greeting(__('course::messages.greeting'))
            ->line(__('course::messages.lesson_published_body', ['title' => $this->lesson->title]))
            ->action(__('course::messages.view_lesson'), url('/lessons/' . $this->lesson->slug));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->lesson->title,
            'url'   => url('/lessons/' . $this->lesson->slug),
        ];
    }
}
