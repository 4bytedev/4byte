<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;
use Packages\Recommend\Services\GorseService;
use Packages\Recommend\Services\GorseUser;

class UserObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    public function created(User $user)
    {
        // if ($user->roles()->exists()) {
        $user->syncRoles(SettingsService::getSiteSettingsField('default_role'));
        // }
        // if(!$user->profile()->exists()) {
        $user->profile()->create([
            'role' => 'Developer',
            'bio' => "Hi, I'm new here 👋",
            'location' => '',
            'website' => '',
            'socials' => [],
        ]);
        // }

        $gorseUser = new GorseUser($user->id, ['article', 'news'], [], $user->username);
        $this->gorse->insertUser($gorseUser);
    }

    public function updated(User $user)
    {
        Cache::forget("user:{$user->id}");
        if ($user->isDirty('avatar')) {
            $oldMedia = $user->getFirstMedia('avatar');
            if ($oldMedia) {
                $oldMedia->delete();
            }
        }
        if ($user->isDirty('cover')) {
            $oldMedia = $user->getFirstMedia('cover');
            if ($oldMedia) {
                $oldMedia->delete();
            }
        }
    }

    public function deleted(User $user)
    {
        $this->gorse->deleteUser($user->id);
        Cache::forget("user:{$user->username}:id");
        Cache::forget("user:{$user->id}");
        Cache::forget("user:{$user->id}:followers");
        Cache::forget("user:{$user->id}:followings");
    }
}
