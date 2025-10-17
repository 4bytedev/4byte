<?php

namespace Packages\Tag\Data;

use Packages\Category\Data\CategoryData;
use Packages\Category\Services\CategoryService;
use Packages\Tag\Models\TagProfile;
use Spatie\LaravelData\Data;

class TagProfileData extends Data
{
    public function __construct(
        public ?int $id,
        public string $description,
        public string $color,
        public CategoryData $category,
    ) {}

    public static function fromModel(TagProfile $tagProfile, bool $setId = false): self
    {
        $categoryService = app(CategoryService::class);

        return new self(
            id: $setId ? $tagProfile->id : 0,
            description: $tagProfile->description,
            color: $tagProfile->color,
            category: $categoryService->getData($tagProfile->category_id)
        );
    }
}
