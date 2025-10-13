<?php

namespace Packages\Entry\Data;

use App\Data\UserData;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Packages\Entry\Models\Entry;
use Packages\Entry\Services\EntryService;
use Spatie\LaravelData\Data;

class EntryData extends Data
{
    public function __construct(
        public int $id,
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
    ) {}

    public static function fromModel(Entry $entry, UserData $user, bool $setId = false, bool $setPublished = true): self
    {
        $entryService = app(EntryService::class);
        $userId = Auth::id();

        return new self(
            id: $setId ? $entry->id : 0,
            slug: $entry->slug,
            content: $entry->content,
            media: $entry->getContentImages(),
            user: $user,
            likes: $entryService->getLikesCount($entry->id),
            dislikes: $entryService->getDislikesCount($entry->id),
            comments: $entryService->getCommentsCount($entry->id),
            isLiked: $entryService->checkLiked($entry->id, $userId),
            isDisliked: $entryService->checkDisliked($entry->id, $userId),
            isSaved: $entryService->checkSaved($entry->id, $userId),
            canUpdate: Gate::allows('update', $entry),
            canDelete: Gate::allows('delete', $entry),
            published_at: $setPublished ? $entry->created_at : null,
        );
    }
}
