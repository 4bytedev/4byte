<?php

namespace Packages\Page\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\Page\Events\PagePublishedEvent;
use Packages\Page\Notifications\PagePublishedNotification;

class PagePublishedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(PagePublishedEvent $event): void
    {
        $page = $event->page;

        $page->user->notify(new PagePublishedNotification($page));
        $page->user->notify(
            Notification::make()
                ->title(__('page::messages.published_title'))
                ->success()
                ->body(__('page::messages.published_body', ['title' => $page->title]))
                ->actions([
                    Action::make('view')
                        ->label(__('page::messages.view_page'))
                        ->url(route('article.view', ['slug' => $page->slug]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
