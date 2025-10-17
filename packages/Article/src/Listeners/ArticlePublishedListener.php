<?php

namespace Packages\Article\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\Article\Events\ArticlePublishedEvent;
use Packages\Article\Notifications\ArticlePublishedNotification;

class ArticlePublishedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(ArticlePublishedEvent $event): void
    {
        $article = $event->article;

        $article->user->notify(new ArticlePublishedNotification($article));
        $article->user->notify(
            Notification::make()
                ->title(__('article::messages.published_title'))
                ->success()
                ->body(__('article::messages.published_body', ['title' => $article->title]))
                ->actions([
                    Action::make('view')
                        ->label(__('article::messages.view_article'))
                        ->url(route('article.view', ['slug' => $article->slug]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
