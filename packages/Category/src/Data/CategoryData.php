<?php

namespace Packages\Category\Data;

use Packages\Category\Models\Category;
use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
    ) {}

    public static function fromModel(Category $category, bool $setId = false): self
    {
        return new self(
            id: $setId ? $category->id : 0,
            name: $category->name,
            slug: $category->slug,
        );
    }
}
