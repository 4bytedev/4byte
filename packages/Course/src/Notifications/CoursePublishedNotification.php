<?php

namespace Packages\Course\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Packages\Course\Models\Course;

class CoursePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Course $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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
            ->subject(__('course::messages.course_published_title'))
            ->greeting(__('course::messages.greeting'))
            ->line(__('course::messages.course_published_body', ['title' => $this->course->title]))
            ->action(__('course::messages.view_course'), url('/courses/' . $this->course->slug));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->course->title,
            'url'   => url('/courses/' . $this->course->slug),
        ];
    }
}
