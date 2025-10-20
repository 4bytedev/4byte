<?php

namespace App\Observers;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;

class UserProfileObserver
{
    /**
     * Handle the "updated" event for the UserProfile model.
     */
    public function updated(UserProfile $userProfile): void
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }

    /**
     * Handle the "deleted" event for the UserProfile model.
     */
    public function deleted(UserProfile $userProfile): void
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }
}
