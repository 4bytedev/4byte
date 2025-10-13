<?php

namespace Packages\News\Data;

use App\Data\UserData;
use App\Services\UserService;
use DateTime;
use Packages\Category\Data\CategoryData;
use Packages\News\Models\News;
use Packages\Tag\Data\TagData;
use Spatie\LaravelData\Data;

class NewsData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public ?string $content,
        public ?string $excerpt,
        public string $image,
        public ?DateTime $published_at,
        public UserData $user,
        /** @var CategoryData[] */
        public array $categories,
        /** @var TagData[] */
        public array $tags,
        public string $type = 'news'
    ) {}

    public static function fromModel(News $news, bool $setId = false): self
    {
        $userService = app(UserService::class);

        return new self(
            id: $setId ? $news->id : 0,
            title: $news->title,
            slug: $news->slug,
            content: $news->content,
            excerpt: $news->excerpt,
            image: $news->image_url,
            published_at: $news->published_at,
            user: $userService->getData($news->user_id),
            categories: CategoryData::collect($news->categories)->all(),
            tags: TagData::collect($news->tags)->all(),
        );
    }
}
