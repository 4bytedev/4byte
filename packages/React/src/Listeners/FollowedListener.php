<?php

namespace Packages\React\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Packages\React\Events\FollowedEvent;
use Packages\React\Models\Follow;

class FollowedListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(FollowedEvent $event): void
    {
        $follow = Follow::where([
            'follower_id'     => $event->follow['follower_id'],
            'followable_id'   => $event->follow['followable_id'],
            'followable_type' => $event->follow['followable_type'],
        ])->firstOrFail();

        $followable = $follow->followable;

        if (in_array(Notifiable::class, class_uses_recursive($followable))) {
            $this->sendNotification($follow, $followable);
        }
    }

    protected function sendNotification(Follow $follow, Model $followable): void
    {
        /* @phpstan-ignore-next-line */
        $followable->notify(
            Notification::make()
                ->title(__('You have a new follower!'))
                ->success()
                ->body(__('user_follow', ['username' => $follow->follower->username]))
                ->actions([
                    Action::make('view')
                        ->label(__('View Profile'))
                        ->url(route('user.view', ['username' => $follow->follower->username]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
