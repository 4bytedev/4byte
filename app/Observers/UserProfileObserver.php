<?php

namespace App\Observers;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;

class UserProfileObserver
{
    public function updated(UserProfile $userProfile)
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }

    public function deleted(UserProfile $userProfile)
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }
}
