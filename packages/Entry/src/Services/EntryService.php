<?php

namespace Packages\Entry\Services;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Packages\Entry\Data\EntryData;
use Packages\Entry\Models\Entry;

class EntryService
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    /**
     * Retrieve entry data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $entryId): EntryData
    {
        $entry = Cache::rememberForever("entry:{$entryId}", function () use ($entryId) {
            return Entry::select(['id', 'slug', 'content', 'user_id', 'created_at'])
                ->findOrFail($entryId);
        });

        $user = $this->userService->getData($entry->user_id);

        return EntryData::fromModel($entry, $user);
    }

    /**
     * Retrieve the ID of a entry by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("entry:{$slug}:id", function () use ($slug) {
            return Entry::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
