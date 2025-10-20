<?php

namespace Packages\Entry\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Packages\Entry\Http\Requests\CreateRequest;
use Packages\Entry\Models\Entry;
use Packages\Entry\Services\EntryService;

class EntryCrudController extends Controller
{
    protected EntryService $entryService;

    public function __construct()
    {
        $this->entryService = app(EntryService::class);
    }

    /**
     * Creates a new Entry.
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $entry = Entry::create([
            'slug'    => Str::uuid(),
            'content' => $data['content'],
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('media')) {
            $entry->addMultipleMediaFromRequest(['media'])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('content');
                });
        }

        return response()->json(['slug' => $entry->slug]);
    }
}
