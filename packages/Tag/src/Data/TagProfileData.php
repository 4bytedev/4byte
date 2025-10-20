<?php

namespace Packages\Tag\Data;

use Packages\Category\Data\CategoryData;
use Packages\Tag\Models\TagProfile;
use Spatie\LaravelData\Data;

class TagProfileData extends Data
{
    /**
     * Summary of __construct.
     *
     * @param int|null $id
     * @param string $description
     * @param string $color
     * @param array<int, CategoryData> $categories
     */
    public function __construct(
        public ?int $id,
        public string $description,
        public string $color,
        public array $categories,
    ) {
    }

    /**
     * Create a TagProfileData instance from a TagProfile model.
     */
    public static function fromModel(TagProfile $tagProfile, bool $setId = false): self
    {
        return new self(
            id: $setId ? $tagProfile->id : 0,
            description: $tagProfile->description,
            color: $tagProfile->color,
            categories: CategoryData::collect(
                $tagProfile->categories->map(fn ($category) => CategoryData::fromModel($category))->toArray()
            )
        );
    }
}
