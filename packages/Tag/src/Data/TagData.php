<?php

namespace Packages\Tag\Data;

use Packages\Tag\Models\Tag;
use Spatie\LaravelData\Data;

class TagData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
    ) {}

    public static function fromModel(Tag $tag, bool $setId = false): self
    {
        return new self(
            id: $setId ? $tag->id : 0,
            name: $tag->name,
            slug: $tag->slug,
        );
    }
}
