<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;
use Packages\Recommend\Classes\GorseUser;
use Packages\Recommend\Services\GorseService;

class UserObserver
{
    protected GorseService $gorse;

    public function __construct()
    {
        $this->gorse = app(GorseService::class);
    }

    /**
     * Handle the "created" event for the User model.
     */
    public function created(User $user): void
    {
        $user->syncRoles(SettingsService::getSiteSettingsField('default_role'));
        $user->profile()->create([
            'role'     => 'Developer',
            'bio'      => "Hi, I'm new here 👋",
            'location' => '',
            'website'  => '',
            'socials'  => [],
        ]);

        $gorseUser = new GorseUser((string) $user->id, ['article', 'entry', 'news'], [], $user->username);
        $this->gorse->insertUser($gorseUser);
    }

    /**
     * Handle the "updated" event for the User model.
     */
    public function updated(User $user): void
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

    /**
     * Handle the "deleted" event for the User model.
     *
     * @return void
     */
    public function deleted(User $user)
    {
        $this->gorse->deleteUser((string) $user->id);
        Cache::forget("user:{$user->username}:id");
        Cache::forget("user:{$user->id}");
        Cache::forget("user:{$user->id}:followers");
        Cache::forget("user:{$user->id}:followings");
    }
}
