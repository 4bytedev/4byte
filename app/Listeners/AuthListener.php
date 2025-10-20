<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Activitylog\Facades\Activity;

class AuthListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(Login|Registered|Logout|PasswordResetLinkSent|PasswordReset $event): void
    {
        /** @var \App\Models\User|null $user */
        $user = $event->user instanceof \App\Models\User ? $event->user : null;

        Activity::causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event(class_basename($event))
            ->log(class_basename($event));
    }
}
