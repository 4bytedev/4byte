<?php

namespace App\Services;

use App\Data\UserData;
use App\Data\UserProfileData;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public function getData(int $userId): ?UserData
    {
        return Cache::rememberForever("user:{$userId}", function () use ($userId) {
            $user = User::query()
                ->select(['id', 'name', 'username', 'created_at'])
                ->findOrFail($userId);

            return UserData::fromModel($user);
        });
    }

    public function getProfileData(int $userId): ?UserProfileData
    {
        return Cache::rememberForever("user:{$userId}:profile", function () use ($userId) {
            $userProfile = UserProfile::query()
                ->where('user_id', $userId)
                ->select(['id', 'role', 'bio', 'location', 'website', 'socials'])
                ->firstOrFail();

            return UserProfileData::fromModel($userProfile);
        });
    }

    public function getId(string $username)
    {
        return Cache::rememberForever("user:{$username}:id", function () use ($username) {
            return User::query()
                ->select(['id'])
                ->where('username', $username)
                ->firstOrFail()->id;
        });
    }

    public function getFollowersCount(int $userId)
    {
        return Cache::rememberForever("user:{$userId}:followers", function () use ($userId) {
            return UserFollow::where('following_id', $userId)->count();
        });
    }

    public function getFollowingCount(int $userId)
    {
        return Cache::rememberForever("user:{$userId}:followings", function () use ($userId) {
            return UserFollow::where('follower_id', $userId)->count();
        });
    }

    public function checkFollowing(?int $userId, int $targetId)
    {
        if (! $userId) {
            return false;
        }

        return UserFollow::where('follower_id', $userId)
            ->where('following_id', $targetId)
            ->exists();
    }

    public function insertFollow(int $userId, int $targetId)
    {
        UserFollow::create([
            'follower_id' => $userId,
            'following_id' => $targetId,
        ]);
        Cache::increment("user:{$targetId}:followers");
        Cache::increment("user:{$userId}:followings");
    }

    public function deleteFollow(int $userId, int $targetId)
    {
        $deleted = UserFollow::where('follower_id', $userId)
            ->where('following_id', $targetId)
            ->delete();
        if ($deleted) {
            Cache::decrement("user:{$targetId}:followers");
            Cache::decrement("user:{$userId}:followings");
        }

        return $deleted;
    }
}
