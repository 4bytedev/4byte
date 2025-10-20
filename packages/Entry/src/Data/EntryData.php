<?php

namespace Packages\Entry\Data;

use App\Data\UserData;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Packages\Entry\Models\Entry;
use Spatie\LaravelData\Data;

class EntryData extends Data
{
    /**
     * @param ?array<int, array{image: string, responsive: string|array<int, string>, srcset: string}> $media
     */
    public function __construct(
        public ?int $id,
        public string $slug,
        public ?string $content,
        public ?array $media,
        public UserData $user,
        public int $likes,
        public int $dislikes,
        public int $comments,
        public bool $isLiked,
        public bool $isDisliked,
        public bool $isSaved,
        public bool $canUpdate,
        public bool $canDelete,
        public ?DateTime $published_at,
        public string $type = 'entry'
    ) {
    }

    /**
     * Create a TagData instance from a Tag model.
     */
    public static function fromModel(Entry $entry, UserData $user, bool $setId = false, bool $setPublished = true): self
    {
        $userId = Auth::id();

        return new self(
            id: $setId ? $entry->id : 0,
            slug: $entry->slug,
            content: $entry->content,
            media: $entry->getContentImages(),
            user: $user,
            likes: $entry->likesCount(),
            dislikes: $entry->dislikesCount(),
            comments: $entry->commentsCount(),
            isLiked: $entry->isLikedBy($userId),
            isDisliked: $entry->isDislikedBy($userId),
            isSaved: $entry->isSavedBy($userId),
            canUpdate: Gate::allows('update', $entry),
            canDelete: Gate::allows('delete', $entry),
            published_at: $setPublished ? $entry->created_at : null,
        );
    }
}
