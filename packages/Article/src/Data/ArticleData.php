<?php

namespace Packages\Article\Data;

use App\Data\UserData;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Packages\Article\Models\Article;
use Packages\Article\Services\ArticleService;
use Packages\Category\Data\CategoryData;
use Packages\Tag\Data\TagData;
use Spatie\LaravelData\Data;

class ArticleData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public ?string $excerpt,
        public ?string $content,
        public array $image,
        public ?DateTime $published_at,
        public UserData $user,
        /** @var CategoryData[] */
        public array $categories,
        /** @var TagData[] */
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
    ) {}

    public static function fromModel(Article $article, UserData $user, bool $setId = false): self
    {
        $articleService = app(ArticleService::class);
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
            likes: $articleService->getLikesCount($article->id),
            dislikes: $articleService->getDislikesCount($article->id),
            comments: $articleService->getCommentsCount($article->id),
            isLiked: $articleService->checkLiked($article->id, $userId),
            isDisliked: $articleService->checkDisliked($article->id, $userId),
            isSaved: $articleService->checkSaved($article->id, $userId),
            canUpdate: Gate::allows('update', $article),
            canDelete: Gate::allows('delete', $article),
            type: $article->status && $article->status == 'PUBLISHED' ? 'article' : 'draft'
        );
    }
}
