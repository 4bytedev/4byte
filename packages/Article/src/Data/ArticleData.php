<?php

namespace Packages\Article\Data;

use App\Data\UserData;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Packages\Article\Models\Article;
use Packages\Category\Data\CategoryData;
use Packages\Tag\Data\TagData;
use Spatie\LaravelData\Data;

class ArticleData extends Data
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null} $image
     * @param array<CategoryData> $categories
     * @param array<TagData> $tags
     * @param array{url: string, date: string} $sources
     */
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public ?string $excerpt,
        public ?string $content,
        public array $image,
        public ?DateTime $published_at,
        public UserData $user,
        public array $categories,
        public array $tags,
        public ?array $sources,
        public int $likes,
        public int $dislikes,
        public int $comments,
        public bool $isLiked,
        public bool $isDisliked,
        public bool $isSaved,
        public bool $canUpdate,
        public bool $canDelete,
        public string $type = 'article'
    ) {
    }

    /**
     * Create a TagData instance from a Tag model.
     */
    public static function fromModel(Article $article, UserData $user, bool $setId = false): self
    {
        $userId = Auth::id();

        return new self(
            id: $setId ? $article->id : 0,
            title: $article->title,
            slug: $article->slug,
            excerpt: $article->excerpt,
            content: $article->content,
            image: $article->getCoverImage(),
            published_at: $article->published_at,
            user: $user,
            categories: CategoryData::collect($article->categories)->all(),
            tags: TagData::collect($article->tags)->all(),
            sources: $article->sources,
            likes: $article->likesCount(),
            dislikes: $article->dislikesCount(),
            comments: $article->commentsCount(),
            isLiked: $article->isLikedBy($userId),
            isDisliked: $article->isDislikedBy($userId),
            isSaved: $article->isSavedBy($userId),
            canUpdate: Gate::allows('update', $article),
            canDelete: Gate::allows('delete', $article),
            type: $article->status === 'PUBLISHED' ? 'article' : 'draft'
        );
    }
}
