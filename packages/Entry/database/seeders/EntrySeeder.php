<?php

namespace Packages\Entry\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryComment;
use Packages\Entry\Models\EntryCommentLike;
use Packages\Entry\Models\EntryDislike;
use Packages\Entry\Models\EntryLike;
use Packages\Entry\Models\EntrySave;

class EntrySeeder extends Seeder
{
    public function run(): void
    {
        Entry::factory(20)->create();

        EntryComment::factory(50)->create();

        EntryLike::factory(50)->create();

        EntryCommentLike::factory(30)->create();

        EntryDislike::factory(20)->create();

        EntrySave::factory(20)->create();
    }
}
