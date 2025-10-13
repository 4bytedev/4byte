<?php

namespace Packages\Article\Data;

use App\Data\UserData;
use App\Services\UserService;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Packages\Article\Models\ArticleComment;
use Packages\Article\Services\ArticleService;
use Spatie\LaravelData\Data;

class ArticleCommentData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ?int $parent,
        public DateTime $created_at,
        public UserData $user,
        public int $replies,
        public int $likes,
        public bool $liked
    ) {}

    public static function fromModel(ArticleComment $articleComment, bool $setReplies = true, bool $setLikes = true, bool $setLiked = true, bool $setId = true): self
    {
        $userService = app(UserService::class);
        $articleService = app(ArticleService::class);

        return new self(
            id: $setId ? $articleComment->id : 0,
            content: $articleComment->content,
            parent: $articleComment->parent_id ?? null,
            created_at: $articleComment->created_at,
            user: $userService->getData($articleComment->user_id),
            replies: $setReplies ? $articleService->getCommentRepliesCount($articleComment->article_id, $articleComment->id) : 0,
            likes: $setReplies ? $articleService->getCommentLikesCount($articleComment->article_id, $articleComment->id) : 0,
            liked: $setLiked ? $articleService->checkCommentLiked($articleComment->article_id, $articleComment->id, Auth::id()) : false
        );
    }
}
