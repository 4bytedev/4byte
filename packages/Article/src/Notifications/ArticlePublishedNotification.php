<?php

namespace Packages\Article\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Packages\Article\Models\Article;

class ArticlePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Article $article;

    /**
     * Create a new notification instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
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
            ->subject(__('article::messages.published_title'))
            ->greeting(__('article::messages.greeting'))
            ->line(__('article::messages.published_body', ['title' => $this->article->title]))
            ->line($this->article->excerpt)
            ->action(__('article::messages.view_article'), url('/articles/' . $this->article->slug));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->article->title,
            'url'   => url('/articles/' . $this->article->slug),
        ];
    }
}
