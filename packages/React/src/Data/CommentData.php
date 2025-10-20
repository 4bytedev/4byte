<?php

namespace Packages\React\Data;

use App\Data\UserData;
use App\Services\UserService;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Packages\React\Models\Comment;
use Spatie\LaravelData\Data;

class CommentData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ?int $parent,
        public DateTime $published_at,
        public UserData $user,
        public int $replies,
        public int $likes,
        public bool $isLiked,
        public ?string $content_type = null,
        public ?string $content_title = null,
        public ?string $content_slug = null,
        public string $type = 'comment'
    ) {
    }

    /**
     * Create a CommentData instance from a Comment model.
     */
    public static function fromModel(Comment $comment, bool $setId = true, bool $setParent = true, bool $setReplies = true, bool $setLikes = true, bool $setLiked = true, bool $setContent = false): self
    {
        $userService = app(UserService::class);

        return new self(
            id: $setId ? $comment->id : 0,
            content: $comment->content,
            parent: $setParent ? $comment->parent_id : null,
            published_at: $comment->created_at,
            user: $userService->getData($comment->user_id),
            replies: $setReplies ? $comment->repliesCount() : 0,
            likes: $setLikes ? $comment->likesCount() : 0,
            isLiked: $setLiked ? $comment->isLikedBy(Auth::id()) : false,
            content_type: $setContent ? strtolower(class_basename($comment->commentable_type)) : null,
            content_title: $setContent ? $comment->commentable?->title : null, /* @phpstan-ignore-line */
            content_slug: $setContent ? $comment->commentable?->slug : null, /* @phpstan-ignore-line */
        );
    }
}
