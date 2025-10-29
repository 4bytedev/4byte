<?php

namespace Packages\Category\Data;

use Illuminate\Support\Facades\Auth;
use Packages\Category\Models\Category;
use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public int $followers,
        public bool $isFollowing,
        public string $type = 'category'
    ) {
    }

    /**
     * Create a CategoryData instance from a Category model.
     */
    public static function fromModel(Category $category, bool $setId = false): self
    {
        return new self(
            id: $setId ? $category->id : 0,
            name: $category->name,
            slug: $category->slug,
            followers: $category->followersCount(),
            isFollowing: $category->isFollowedBy(Auth::id())
        );
    }
}
