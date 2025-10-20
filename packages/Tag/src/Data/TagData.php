<?php

namespace Packages\Tag\Data;

use Illuminate\Support\Facades\Auth;
use Packages\Tag\Models\Tag;
use Spatie\LaravelData\Data;

class TagData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public int $followers,
        public bool $isFollowing
    ) {
    }

    /**
     * Create a TagData instance from a Tag model.
     */
    public static function fromModel(Tag $tag, bool $setId = false): self
    {
        return new self(
            id: $setId ? $tag->id : 0,
            name: $tag->name,
            slug: $tag->slug,
            followers: $tag->followersCount(),
            isFollowing: $tag->isFollowedBy(Auth::id())
        );
    }
}
