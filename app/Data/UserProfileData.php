<?php

namespace App\Data;

use App\Models\UserProfile;
use Spatie\LaravelData\Data;

class UserProfileData extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $role,
        public ?string $bio,
        public ?string $location,
        public ?string $website,
        public ?array $socials,
        public array $cover,
    ) {
    }

    public static function fromModel(UserProfile $userProfile, bool $setId = false): self
    {
        return new self(
            id: $setId ? $userProfile->id : 0,
            role: $userProfile->role,
            bio: $userProfile->bio,
            location: $userProfile->location,
            website: $userProfile->website,
            socials: $userProfile->socials,
            cover: $userProfile->getCoverImage()
        );
    }
}
