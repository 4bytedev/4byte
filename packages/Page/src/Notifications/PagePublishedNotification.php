<?php

namespace Packages\Page\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Packages\Page\Models\Page;

class PagePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Page $page;

    /**
     * Create a new notification instance.
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
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
            ->subject(__('page::messages.published_title'))
            ->greeting(__('page::messages.greeting'))
            ->line(__('page::messages.published_body', ['title' => $this->page->title]))
            ->line($this->page->excerpt)
            ->action(__('page::messages.view_page'), url('/pages/' . $this->page->slug));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->page->title,
            'url'   => url('/pages/' . $this->page->slug),
        ];
    }
}
