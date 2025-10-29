<?php

namespace App\Data;

use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $username,
        public ?string $avatar,
        public int $followers,
        public int $followings,
        public bool $isFollowing,
        public DateTime $created_at,
        public string $type = 'user'
    ) {
    }

    /**
     * Create a UserData instance from a User model.
     */
    public static function fromModel(User $user, bool $setId = false): self
    {
        return new self(
            id: $setId ? $user->id : 0,
            name: $user->name,
            username: $user->username,
            avatar: $user->getAvatarImage(),
            followers: $user->followersCount(),
            followings: $user->followingsCount(),
            isFollowing: $user->isFollowedBy(Auth::id()),
            created_at: $user->created_at
        );
    }
}
