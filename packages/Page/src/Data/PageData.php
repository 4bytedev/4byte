<?php

namespace Packages\Page\Data;

use App\Data\UserData;
use DateTime;
use Illuminate\Support\Facades\Gate;
use Packages\Page\Models\Page;
use Spatie\LaravelData\Data;

class PageData extends Data
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string} $image
     */
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public ?string $content,
        public ?string $excerpt,
        public array $image,
        public UserData $user,
        public bool $canUpdate,
        public bool $canDelete,
        public ?DateTime $published_at,
        public string $type = 'page'
    ) {
    }

    /**
     * Create a PageData instance from a Page model.
     */
    public static function fromModel(Page $page, UserData $user, bool $setId = false): self
    {
        return new self(
            id: $setId ? $page->id : 0,
            title: $page->title,
            slug: $page->slug,
            content: $page->content,
            excerpt: $page->excerpt,
            image: $page->getCoverImage(),
            user: $user,
            canUpdate: Gate::allows('update', $page),
            canDelete: Gate::allows('delete', $page),
            published_at: $page->published_at,
        );
    }
}
