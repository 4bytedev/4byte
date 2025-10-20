<?php

namespace Packages\News\Data;

use App\Data\UserData;
use App\Services\UserService;
use DateTime;
use Illuminate\Support\Facades\Gate;
use Packages\Category\Data\CategoryData;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Spatie\LaravelData\Data;

class NewsData extends Data
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null} $image
     * @param array<CategoryData> $categories
     * @param array<TagData> $tags
     */
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public ?string $content,
        public ?string $excerpt,
        public array $image,
        public ?DateTime $published_at,
        public UserData $user,
        public array $categories,
        public array $tags,
        public bool $canUpdate,
        public bool $canDelete,
        public string $type = 'news'
    ) {
    }

    /**
     * Create a NewsData instance from a News model.
     */
    public static function fromModel(News $news, bool $setId = false): self
    {
        $userService = app(UserService::class);

        return new self(
            id: $setId ? $news->id : 0,
            title: $news->title,
            slug: $news->slug,
            content: $news->content,
            excerpt: $news->excerpt,
            image: $news->getCoverImage(),
            published_at: $news->published_at,
            user: $userService->getData($news->user_id),
            categories: CategoryData::collect($news->categories)->all(),
            tags: TagData::collect($news->tags)->all(),
            canUpdate: Gate::allows('update', $news),
            canDelete: Gate::allows('delete', $news),
        );
    }
}
