<?php

namespace App\Data;

use App\Models\User;
use DateTime;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $username,
        public ?string $avatar,
        public DateTime $created_at
    ) {}

    public static function fromModel(User $user, bool $setId = false): self
    {
        return new self(
            id: $setId ? $user->id : 0,
            name: $user->name,
            username: $user->username,
            avatar: $user->getAvatarImage(),
            created_at: $user->created_at
        );
    }
}
