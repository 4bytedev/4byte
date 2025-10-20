<?php

namespace Packages\Category\Data;

use Packages\Category\Models\CategoryProfile;
use Spatie\LaravelData\Data;

class CategoryProfileData extends Data
{
    public function __construct(
        public ?int $id,
        public string $description,
        public string $color,
    ) {
    }

    /**
     * Create a CategoryProfileData instance from a CategoryProfile model.
     */
    public static function fromModel(CategoryProfile $categoryProfile, bool $setId = false): self
    {
        return new self(
            id: $setId ? $categoryProfile->id : 0,
            description: $categoryProfile->description,
            color: $categoryProfile->color,
        );
    }
}
