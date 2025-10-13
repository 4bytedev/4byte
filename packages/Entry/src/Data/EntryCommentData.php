<?php

namespace Packages\Entry\Data;

use App\Data\UserData;
use App\Services\UserService;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Packages\Entry\Models\EntryComment;
use Packages\Entry\Services\EntryService;
use Spatie\LaravelData\Data;

class EntryCommentData extends Data
{
    public function __construct(
        public ?int $id,
        public string $content,
        public ?int $parent,
        public DateTime $created_at,
        public UserData $user,
        public int $replies,
        public int $likes,
        public bool $isLiked
    ) {}

    public static function fromModel(EntryComment $entryComment, bool $setReplies = true, bool $setLikes = true, bool $setLiked = true, bool $setId = true): self
    {
        $userService = app(UserService::class);
        $entryService = app(EntryService::class);

        return new self(
            id: $setId ? $entryComment->id : 0,
            content: $entryComment->content,
            parent: $entryComment->parent_id ?? null,
            created_at: $entryComment->created_at,
            user: $userService->getData($entryComment->user_id),
            replies: $setReplies ? $entryService->getCommentRepliesCount($entryComment->entry_id, $entryComment->id) : 0,
            likes: $setReplies ? $entryService->getCommentLikesCount($entryComment->entry_id, $entryComment->id) : 0,
            isLiked: $setLiked ? $entryService->checkCommentLiked($entryComment->entry_id, $entryComment->id, Auth::id()) : false
        );
    }
}
