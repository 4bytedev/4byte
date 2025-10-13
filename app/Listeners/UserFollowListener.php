<?php

namespace App\Listeners;

use App\Events\UserFollowEvent;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserFollowListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserFollowEvent $event): void
    {
        $user = $event->user;
        $targetId = $event->targetId;
        $targetUser = User::where('id', $targetId)->firstOrFail();

        $targetUser->notify(
            Notification::make()
                ->title(__('You have a new follower!'))
                ->success()
                ->body(__('user_follow.', ['username' => $user->username]))
                ->actions([
                    Action::make('view-target')
                        ->label('View User')
                        ->url(route('user.view', ['username' => $user->username]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
